<?php
session_start();
include '../Private/poketrader_db.connection.php';

$userId = $_SESSION['userId'] ?? 0;
if ($userId <= 0) {
    header('Location: ../index.php?page=auction&msg=You+must+log+in');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=auction');
    exit;
}

$cardId = (int)($_POST['card_id'] ?? 0);
$amount = (float)($_POST['amount'] ?? 0);
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

// Auction still active?
$createdAt = new DateTime($card['created_at']);
$now = new DateTime();
$diff = $now->getTimestamp() - $createdAt->getTimestamp();
if ($diff >= 600) {
    header('Location: ../index.php?page=auction&msg=Auction+ended');
    exit;
}

// Huidige hoogste bod ophalen
$stmtHighest = $pdo->prepare('SELECT * FROM bids WHERE card_id = :card_id ORDER BY amount DESC, created_at ASC LIMIT 1');
$stmtHighest->execute([':card_id' => $cardId]);
$highestRow = $stmtHighest->fetch(PDO::FETCH_ASSOC);

$currentHighest = max((float)$card['price'], (float)($highestRow['amount'] ?? 0));
$prevUserId = $highestRow['userId'] ?? null;
$prevAmount = $highestRow['amount'] ?? 0;

if ($amount <= $currentHighest) {
    header('Location: ../index.php?page=auction&msg=Bid+must+be+higher+than+' . $currentHighest);
    exit;
}

// Wallet check
$stmtBalance = $pdo->prepare('SELECT COALESCE(SUM(pokedollars),0) AS balance FROM wallet WHERE userId = :userId');
$stmtBalance->execute([':userId' => $userId]);
$balance = (float)($stmtBalance->fetchColumn() ?? 0);

$required = $prevUserId === $userId ? $amount - $prevAmount : $amount;
if ($required > $balance) {
    header('Location: ../index.php?page=auction&msg=Not+enough+Poke$');
    exit;
}

$pdo->beginTransaction();
try {
    // Charge huidige gebruiker
    if ($required > 0) {
        $stmtCharge = $pdo->prepare('INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :pokedollars)');
        $stmtCharge->execute([':userId' => $userId, ':pokedollars' => -$required]);
    }

    // Refund vorige hoogste bieder
    if ($prevUserId && $prevUserId !== $userId) {
        $stmtRefund = $pdo->prepare('INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :pokedollars)');
        $stmtRefund->execute([':userId' => $prevUserId, ':pokedollars' => $prevAmount]);
    }

    // Opslaan bod
    $stmtInsert = $pdo->prepare('INSERT INTO bids (card_id, userId, amount, created_at) VALUES (:card_id, :userId, :amount, NOW())');
    $stmtInsert->execute([':card_id'=>$cardId, ':userId'=>$userId, ':amount'=>$amount]);

    $pdo->commit();
    header('Location: ../index.php?page=auction&msg=Bid+placed+successfully');
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: ../index.php?page=auction&msg=Error+placing+bid');
}
