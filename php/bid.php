<?php
session_start();
include '../Private/poketrader_db.connection.php';

// Logged-in user
$userId = $_SESSION['userId'] ?? 0;
if ($userId <= 0) {
    header('Location: ../index.php?page=auction&msg=You+must+log+in');
    exit;
}

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=auction');
    exit;
}

$cardId = isset($_POST['card_id']) ? (int)$_POST['card_id'] : 0;
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

if ($cardId <= 0 || $amount <= 0) {
    header('Location: ../index.php?page=auction&msg=Invalid+bid');
    exit;
}

// Get card
$stmtCard = $pdo->prepare('SELECT * FROM pokecards WHERE id = :id');
$stmtCard->execute([':id' => $cardId]);
$card = $stmtCard->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    header('Location: ../index.php?page=auction&msg=Card+not+found');
    exit;
}

// Auction still active? (10 min)
$createdAt = new DateTime($card['created_at']);
$now = new DateTime();
$diff = $now->getTimestamp() - $createdAt->getTimestamp();
$auctionActive = $diff < 600;

if (!$auctionActive) {
    header('Location: ../index.php?page=auction&msg=Auction+ended');
    exit;
}

$startPrice = (float)$card['price'];

try {
    $pdo->beginTransaction();

    // Huidige hoogste bod
    $stmtHighest = $pdo->prepare(
        'SELECT * FROM bids WHERE card_id = :card_id ORDER BY amount DESC, created_at ASC LIMIT 1'
    );
    $stmtHighest->execute([':card_id' => $cardId]);
    $highestRow = $stmtHighest->fetch(PDO::FETCH_ASSOC);

    $currentHighest = $startPrice;
    $prevUserId = null;
    $prevAmount = 0;

    if ($highestRow) {
        $currentHighest = max($startPrice, (float)$highestRow['amount']);
        $prevUserId = (int)$highestRow['userId'];
        $prevAmount = (float)$highestRow['amount'];
    }

    if ($amount <= $currentHighest) {
        $pdo->rollBack();
        header('Location: ../index.php?page=auction&msg=Bid+must+be+higher+than+' . $currentHighest);
        exit;
    }

    // Wallet saldo van gebruiker
    $stmtBalance = $pdo->prepare(
        'SELECT COALESCE(SUM(pokedollars),0) AS balance FROM wallet WHERE userId = :userId'
    );
    $stmtBalance->execute([':userId' => $userId]);
    $balanceRow = $stmtBalance->fetch(PDO::FETCH_ASSOC);
    $balance = (float)($balanceRow['balance'] ?? 0);

    $required = $amount;
    if ($prevUserId === $userId) {
        $required = $amount - $prevAmount;
    }

    if ($required > $balance) {
        $pdo->rollBack();
        header('Location: ../index.php?page=auction&msg=Not+enough+Poke$');
        exit;
    }

    // Charge huidige gebruiker
    if ($required > 0) {
        $stmtCharge = $pdo->prepare(
            'INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :pokedollars)'
        );
        $stmtCharge->execute([
            ':userId' => $userId,
            ':pokedollars' => -$required
        ]);
    }

    // Refund vorige hoogste bieder als het niet jij was
    if ($prevUserId && $prevUserId !== $userId) {
        $stmtRefund = $pdo->prepare(
            'INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :pokedollars)'
        );
        $stmtRefund->execute([
            ':userId' => $prevUserId,
            ':pokedollars' => $prevAmount
        ]);
    }

    // Opslaan bod
    $stmtInsert = $pdo->prepare("INSERT INTO bids (card_id, userId, amount, created_at) VALUES (:card_id, :userId, :amount, NOW())");
    $stmtInsert->execute([
        ':card_id' => $cardId,
        ':userId'  => $userId,
        ':amount'  => $amount
    ]);

    // --- Check alle kaarten of veiling klaar is ---
    $stmtCards = $pdo->query("SELECT * FROM pokecards ORDER BY id DESC");
    $allCards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allCards as $c) {
        $cid = $c['id'];
        $winnerId = $c['winner_userId'] ?? null;
        $createdAtC = new DateTime($c['created_at']);
        $diffC = $now->getTimestamp() - $createdAtC->getTimestamp();
        $activeC = $diffC < 600;

        if (!$activeC && empty($winnerId)) {
            $stmtHighestC = $pdo->prepare(
                'SELECT b.amount, b.userId, u.name 
                 FROM bids b 
                 JOIN users u ON u.userId = b.userId
                 WHERE b.card_id = :card_id
                 ORDER BY b.amount DESC, b.created_at ASC
                 LIMIT 1'
            );
            $stmtHighestC->execute([':card_id' => $cid]);
            $highestC = $stmtHighestC->fetch(PDO::FETCH_ASSOC);

            if ($highestC) {
                $winnerIdC = (int)$highestC['userId'];
                $highestBidC = (float)$highestC['amount'];

                // Winner opslaan in kolom userId
                $stmtSetWinner = $pdo->prepare('UPDATE pokecards SET userId = :winner WHERE id = :card_id');
                $stmtSetWinner->execute([
                    ':winner' => $winnerIdC,
                    ':card_id' => $cid
                ]);

                // Inbox melding sturen
                $melding = "Congratulations! You have won: " . $c['name'] . " for €" . number_format($highestBidC, 2, ',', '.');

                $stmtInbox = $pdo->prepare(
                    'INSERT INTO klantinbox (melding, PokecardId, price, userId, status, PokecardImg, PokecardName) 
         VALUES (:melding, :PokecardId, :price, :userId, :status, :PokecardImg, :PokecardName)'
                );
                $stmtInbox->execute([
                    ':melding' => $melding,
                    ':PokecardId' => $cid,
                    ':price' => $highestBidC,
                    ':userId' => $winnerIdC,
                    ':status' => 'open',
                    ':PokecardImg' => $c['img'],
                    ':PokecardName' => $c['name']
                ]);
            }

        }
    }

    $pdo->commit();
    header('Location: ../index.php?page=auction&msg=Bid+placed+successfully');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Location: ../index.php?page=auction&msg=Error+placing+bid');
    exit;
}
