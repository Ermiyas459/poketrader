<?php
session_start();
include '../../Private/connectionpoke.php';

// Logged-in user
$userId = $_SESSION['userId'] ?? 0;
if ($userId <= 0) {
    header('Location: ../index.php?page=auction');
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
    header('Location: ../index.php?page=auction');
    exit;
}

// Card
$stmtCard = $pdo->prepare('SELECT * FROM pokecards WHERE id = :id');
$stmtCard->execute([':id' => $cardId]);
$card = $stmtCard->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    header('Location: ../index.php?page=auction');
    exit;
}

// Auction still running? (10 min)
$createdAt = new DateTime($card['created_at']);
$now       = new DateTime();
$diff      = $now->getTimestamp() - $createdAt->getTimestamp();
if ($diff > 600) {
    header('Location: ../index.php?page=auction');
    exit;
}

$startPrice = (float)$card['price'];

try {
    $pdo->beginTransaction();

    // Current highest bid (if any)
    $stmtHighest = $pdo->prepare(
        'SELECT * FROM bids 
         WHERE card_id = :card_id 
         ORDER BY amount DESC, created_at ASC 
         LIMIT 1'
    );
    $stmtHighest->execute([':card_id' => $cardId]);
    $highestRow = $stmtHighest->fetch(PDO::FETCH_ASSOC);

    $currentHighestAmount = $startPrice;
    if ($highestRow) {
        $currentHighestAmount = max($currentHighestAmount, (float)$highestRow['amount']);
    }

    if ($amount <= $currentHighestAmount) {
        $pdo->rollBack();
        header('Location: ../index.php?page=auction');
        exit;
    }

    // Current balance
    $stmtBalance = $pdo->prepare(
        'SELECT COALESCE(SUM(pokedollars), 0) AS balance 
         FROM wallet 
         WHERE userId = :userId'
    );
    $stmtBalance->execute([':userId' => $userId]);
    $balanceRow = $stmtBalance->fetch(PDO::FETCH_ASSOC);
    $balance = (float)($balanceRow['balance'] ?? 0);

    // How much do we actually need to charge now?
    $required = 0;

    if ($highestRow) {
        $prevUserId = (int)$highestRow['user_id'];
        $prevAmount = (float)$highestRow['amount'];

        if ($prevUserId === $userId) {
            // You are already highest bidder → pay only the difference
            $required = $amount - $prevAmount;
        } else {
            // New highest bidder → pay full amount
            $required = $amount;

            // Refund previous highest bidder
            $stmtRefund = $pdo->prepare(
                'INSERT INTO wallet (userId, pokedollars)
                 VALUES (:userId, :pokedollars)'
            );
            $stmtRefund->execute([
                ':userId'      => $prevUserId,
                ':pokedollars' => $prevAmount,
            ]);
        }
    } else {
        // First bid
        $required = $amount;
    }

    // Check balance vs required
    if ($required > $balance) {
        $pdo->rollBack();
        $msg = 'Not enough Poke$! Required: ' . $required . ' | You have: ' . $balance;
        header('Location: ../index.php?page=auction');
        exit;
    }

    // Charge user
    if ($required > 0) {
        $stmtCharge = $pdo->prepare(
            'INSERT INTO wallet (userId, pokedollars)
             VALUES (:userId, :pokedollars)'
        );
        $stmtCharge->execute([
            ':userId'      => $userId,
            ':pokedollars' => -$required,
        ]);
    }

    // Save bid
    $stmtInsertBid = $pdo->prepare(
        'INSERT INTO bids (card_id, user_id, amount)
         VALUES (:card_id, :user_id, :amount)'
    );
    $stmtInsertBid->execute([
        ':card_id' => $cardId,
        ':user_id' => $userId,
        ':amount'  => $amount,
    ]);

    $pdo->commit();

    header('Location: ../index.php?page=auction');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Location: ../index.php?page=auction');
    exit;
}
