<?php
session_start();
date_default_timezone_set('Europe/Amsterdam');
include '../Private/poketrader_db.connection.php';

echo "<h1>Test Veiling Afsluiten</h1>";

// Haal alle open veilingen op
$openAuctions = $pdo->query("SELECT * FROM auctions WHERE status = 'open'")->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Open veilingen: " . count($openAuctions) . "</h2>";

foreach ($openAuctions as $auction) {
    $auctionId = $auction['auctionId'];
    $pokecardId = $auction['pokecardId'];
    $endTime = $auction['endTime'];

    echo "<hr>";
    echo "<h3>Veiling ID: $auctionId</h3>";
    echo "PokecardId: $pokecardId<br>";
    echo "Eindtijd: $endTime<br>";
    echo "Huidige tijd: " . date('Y-m-d H:i:s') . "<br>";

    $currentTime = time();
    $endTimeStamp = strtotime($endTime);

    if ($currentTime >= $endTimeStamp) {
        echo "<strong style='color:green;'>VERLOPEN - Wordt afgesloten</strong><br>";
    } else {
        echo "<strong style='color:orange;'>NOG ACTIEF - " . ($endTimeStamp - $currentTime) . " seconden over</strong><br>";
        echo "<a href='test_veiling_afsluiten.php?force=$auctionId'>FORCEER AFSLUITEN (TEST)</a><br>";
        continue;
    }

    // Haal hoogste bod op
    $bidResult = $pdo->query("SELECT userId, amount FROM bids WHERE auctionId = $auctionId ORDER BY amount DESC LIMIT 1");
    $highestBid = $bidResult->fetch(PDO::FETCH_ASSOC);

    if ($highestBid) {
        $winnerId = $highestBid['userId'];
        $bidAmount = $highestBid['amount'];

        echo "Winnaar: userId $winnerId<br>";
        echo "Hoogste bod: $bidAmount euro<br>";

        // Check wallet
        $walletResult = $pdo->query("SELECT wallet FROM users WHERE id = $winnerId");
        $userWallet = $walletResult->fetch(PDO::FETCH_ASSOC);
        $wallet = $userWallet['wallet'];

        echo "Wallet winnaar: $wallet euro<br>";

        if ($wallet >= $bidAmount) {
            echo "<strong style='color:blue;'>START AFHANDELING...</strong><br>";

            // 1. Geld aftrekken
            $pdo->exec("UPDATE users SET wallet = wallet - $bidAmount WHERE id = $winnerId");
            echo "1. Geld afgetrokken<br>";

            // 2. Pokecard eigenaar veranderen
            $pdo->exec("UPDATE pokecards SET userId = $winnerId WHERE id = $pokecardId");
            echo "2. Pokecard eigenaar veranderd naar userId $winnerId<br>";

            // 3. Haal kaartinfo op
            $cardResult = $pdo->query("SELECT name, img FROM pokecards WHERE id = $pokecardId");
            $card = $cardResult->fetch(PDO::FETCH_ASSOC);
            $cardName = $card['name'];
            $cardImg = $card['img'];

            echo "3. Kaartinfo opgehaald: $cardName<br>";

            // 4. Inbox melding
            $melding = "Je hebt de veiling gewonnen voor $cardName voor $bidAmount euro!";
            $insertQuery = "INSERT INTO klantinbox (userId, melding, PokecardId, PokecardImg, PokecardName, price, status) VALUES ($winnerId, '$melding', $pokecardId, '$cardImg', '$cardName', $bidAmount, 'open')";

            echo "Query: $insertQuery<br>";

            try {
                $pdo->exec($insertQuery);
                echo "4. Inbox melding toegevoegd<br>";
            } catch (Exception $e) {
                echo "4. FOUT bij inbox: " . $e->getMessage() . "<br>";
            }

            // 5. Veiling sluiten
            $pdo->exec("UPDATE auctions SET status = 'closed' WHERE auctionId = $auctionId");
            echo "5. Veiling gesloten<br>";

            echo "<strong style='color:green;'>KLAAR!</strong><br>";

        } else {
            echo "<strong style='color:red;'>Niet genoeg geld!</strong><br>";
        }
    } else {
        echo "<strong style='color:red;'>Geen biedingen gevonden</strong><br>";
    }
}

// FORCE optie voor testen
if (isset($_GET['force'])) {
    $forceId = intval($_GET['force']);
    echo "<hr><h2 style='color:red;'>FORCE CLOSE VEILING $forceId</h2>";

    // Zet eindtijd op nu
    $pdo->exec("UPDATE auctions SET endTime = NOW() WHERE auctionId = $forceId");
    echo "Eindtijd aangepast naar NU<br>";
    echo "<a href='test_veiling_afsluiten.php'>Refresh en sluit af</a>";
}

echo "<hr>";
echo "<h2>Check Database:</h2>";
echo "<a href='../index.php?page=klantinbox'>Bekijk Inbox</a> | ";
echo "<a href='test_veiling_afsluiten.php'>Refresh</a>";
?>

