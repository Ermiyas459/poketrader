<?php
require '../Private/poketrader_db.connection.php';

if (!isset($_GET['auctionId'])) {
    exit;
}

$auctionId = intval($_GET['auctionId']);

$stmt = $pdo->prepare("
    SELECT a.*, b.userId AS highestUser, b.amount AS highestAmount
    FROM auctions a
    LEFT JOIN bids b ON b.auctionId = a.auctionId
    WHERE a.auctionId = :aid
    ORDER BY b.amount DESC
    LIMIT 1
");
$stmt->execute([':aid' => $auctionId]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    exit;
}

$winnerId = $auction['highestUser'];
$winningBid = $auction['highestAmount'];

// 2. als er geen bod is: auction sluiten zonder winnaar
if (!$winnerId) {

    $stmtClose = $pdo->prepare("UPDATE auctions SET status = 'closed', winner_userId = NULL WHERE auctionId = :aid");
    $stmtClose->execute([':aid' => $auctionId]);

    exit;
}

// 3. Kaart eigenaar veranderen
$stmtUpdateCard = $pdo->prepare("
    UPDATE pokecards
    SET ownerId = :winner
    WHERE cardId = :cid
");
$stmtUpdateCard->execute([
    ':winner' => $winnerId,
    ':cid' => $auction['cardId']
]);

// 4. winnaar geld afschrijven
$stmtWallet = $pdo->prepare("
    INSERT INTO wallet (userId, pokedollars, reason)
    VALUES (:uid, :amount, 'Auction Win')
");
$stmtWallet->execute([
    ':uid' => $winnerId,
    ':amount' => -$winningBid
]);

// 5. inbox melding voor winnaar
$stmtInbox = $pdo->prepare("
    INSERT INTO inbox (userId, title, message, created_at)
    VALUES (:uid, :title, :msg, NOW())
");
$stmtInbox->execute([
    ':uid'   => $winnerId,
    ':title' => "Gefeliciteerd! Je hebt een veiling gewonnen 🎉",
    ':msg'   => "Je hebt de kaart '{$auction['cardId']}' gewonnen met een bod van {$winningBid} Pokédollars."
]);

// 6. veiling sluiten + winnaar opslaan
$stmtFinish = $pdo->prepare("
    UPDATE auctions
    SET status = 'closed',
        winner_userId = :winner
    WHERE auctionId = :aid
");
$stmtFinish->execute([
    ':winner' => $winnerId,
    ':aid'    => $auctionId
]);

// 7. klaar → terugsturen
header("Location: ../index.php?page=auction&auctionId=" . $auctionId);
exit;
?>

