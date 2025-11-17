<?php
include '../Private/poketrader_db.connection.php';


$now = new DateTime();


$stmtCards = $pdo->query("SELECT * FROM pokecards ORDER BY id DESC");
$allCards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);

foreach ($allCards as $c) {
    $cid = $c['id'];
    $winnerId = $c['userId'] ?? null;
    $createdAtC = new DateTime($c['created_at']);
    $diffC = $now->getTimestamp() - $createdAtC->getTimestamp();
    $activeC = $diffC < 120;


    if (!$activeC && empty($winnerId)) {

        $stmtHighest = $pdo->prepare(
            'SELECT b.amount, b.userId, u.name 
             FROM bids b 
             JOIN users u ON u.userId = b.userId
             WHERE b.card_id = :card_id
             ORDER BY b.amount DESC, b.created_at ASC
             LIMIT 1'
        );
        $stmtHighest->execute([':card_id' => $cid]);
        $highest = $stmtHighest->fetch(PDO::FETCH_ASSOC);

        if ($highest) {
            $winnerIdC = (int)$highest['userId'];
            $highestBidC = (float)$highest['amount'];

            try {
                $pdo->beginTransaction();


                $stmtSetOwner = $pdo->prepare('UPDATE pokecards SET userId = :winner WHERE id = :card_id');
                $stmtSetOwner->execute([
                    ':winner' => $winnerIdC,
                    ':card_id' => $cid
                ]);


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
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();

            }
        }

    }
}

