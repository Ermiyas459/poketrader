<?php
date_default_timezone_set('Europe/Amsterdam');
include '../Private/poketrader_db.connection.php';

$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;

echo "<h1>DEBUG VEILING SYSTEEM</h1>";

// --- DEEL 1: Verwerk een nieuw bod ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auctionId'], $_POST['amount'])) {
    if (!$userId) {
        header("Location: ../index.php?page=login");
        exit;
    }

    $auctionId = intval($_POST['auctionId']);
    $bidAmount = floatval($_POST['amount']);

    echo "<h2>Nieuw bod ontvangen</h2>";
    echo "AuctionId: $auctionId<br>";
    echo "Bod: $bidAmount<br>";

    $stmt = $pdo->query("SELECT * FROM auctions WHERE auctionId = $auctionId");
    $auction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($auction && $auction['status'] === 'open' && time() < strtotime($auction['endTime'])) {
        $currentHighest = floatval($auction['highest_bid']);

        if ($bidAmount > $currentHighest) {
            $pdo->exec("INSERT INTO bids (auctionId, userId, amount, bid_time) VALUES ($auctionId, $userId, $bidAmount, NOW())");
            $pdo->exec("UPDATE auctions SET highest_bid = $bidAmount, highest_bidder = $userId WHERE auctionId = $auctionId");

            echo "<p style='color:green;'>Bod geplaatst!</p>";
            $_SESSION['success'] = "Je bod van $bidAmount is geplaatst!";
        } else {
            echo "<p style='color:red;'>Bod te laag!</p>";
            $_SESSION['error'] = "Je bod moet hoger zijn dan $currentHighest";
        }
    }

    echo "<hr>";
}

// --- DEEL 2: Check verlopen veilingen ---
echo "<h2>Checken verlopen veilingen</h2>";

$openAuctions = $pdo->query("SELECT * FROM auctions WHERE status = 'open'")->fetchAll(PDO::FETCH_ASSOC);

echo "Aantal open veilingen: " . count($openAuctions) . "<br><br>";

foreach ($openAuctions as $auction) {
    $auctionId = $auction['auctionId'];
    $pokecardId = $auction['pokecardId'];
    $endTime = $auction['endTime'];

    $currentTime = time();
    $endTimeStamp = strtotime($endTime);

    echo "<strong>Veiling $auctionId:</strong><br>";
    echo "Eindtijd: $endTime<br>";
    echo "Nu: " . date('Y-m-d H:i:s') . "<br>";

    if ($currentTime >= $endTimeStamp) {
        echo "<span style='color:green;'>VERLOPEN - wordt afgesloten</span><br>";

        // Haal hoogste bod op uit bids
        $bidResult = $pdo->query("SELECT userId, amount FROM bids WHERE auctionId = $auctionId ORDER BY amount DESC LIMIT 1");
        $highestBid = $bidResult->fetch(PDO::FETCH_ASSOC);

        if ($highestBid) {
            $winnerId = $highestBid['userId'];
            $bidAmount = $highestBid['amount'];

            echo "Winnaar: userId $winnerId met bod $bidAmount euro<br>";

            // Check wallet - haal pokedollars op
            $walletResult = $pdo->query("SELECT pokedollars FROM wallet WHERE userId = $winnerId");
            $walletData = $walletResult->fetch(PDO::FETCH_ASSOC);
            $pokedollars = $walletData['pokedollars'];

            echo "Pokedollars: $pokedollars<br>";

            if ($pokedollars >= $bidAmount) {

                // 1. Trek geld af - UPDATE wallet tabel
                echo "1. Trek $bidAmount pokedollars af van userId $winnerId<br>";
                $pdo->exec("UPDATE wallet SET pokedollars = pokedollars - $bidAmount WHERE userId = $winnerId");

                // Check nieuwe saldo
                $newWalletCheck = $pdo->query("SELECT pokedollars FROM wallet WHERE userId = $winnerId");
                $newWalletData = $newWalletCheck->fetch(PDO::FETCH_ASSOC);
                echo "Nieuwe saldo: " . $newWalletData['pokedollars'] . " pokedollars<br>";

                // 2. Pokecard eigenaar veranderen
                echo "2. Verander pokecard $pokecardId naar userId $winnerId<br>";

                // Check huidige eigenaar
                $oldOwnerCheck = $pdo->query("SELECT userId FROM pokecards WHERE id = $pokecardId");
                $oldOwner = $oldOwnerCheck->fetch(PDO::FETCH_ASSOC);
                echo "Oude eigenaar: userId " . $oldOwner['userId'] . "<br>";

                $pdo->exec("UPDATE pokecards SET userId = $winnerId WHERE id = $pokecardId");

//                // Check nieuwe eigenaar
//                $newOwnerCheck = $pdo->query("SELECT userId FROM pokecards WHERE id = $pokecardId");
//                $newOwner = $newOwnerCheck->fetch(PDO::FETCH_ASSOC);
//                echo "Nieuwe eigenaar: userId " . $newOwner['userId'] . "<br>";

                // 3. Haal kaartinfo op
                $cardResult = $pdo->query("SELECT name, img FROM pokecards WHERE id = $pokecardId");
                $card = $cardResult->fetch(PDO::FETCH_ASSOC);
                $cardName = $card['name'];
                $cardImg = $card['img'];

                echo "3. Kaart info: $cardName<br>";

                // 4. Inbox melding
                $melding = "Je hebt de veiling gewonnen voor $cardName voor $bidAmount pokedollars!";

                // Escape strings
                $meldingEscaped = addslashes($melding);
                $cardNameEscaped = addslashes($cardName);
                $cardImgEscaped = addslashes($cardImg);

                $insertQuery = "INSERT INTO klantinbox (userId, melding, PokecardId, PokecardImg, PokecardName, price, status) VALUES ($winnerId, '$meldingEscaped', $pokecardId, '$cardImgEscaped', '$cardNameEscaped', $bidAmount, 'open')";

                echo "4. Inbox query wordt uitgevoerd...<br>";

                try {
                    $result = $pdo->exec($insertQuery);
                    echo "<span style='color:green;'>Inbox melding toegevoegd! (rows: $result)</span><br>";

                    // Check of het echt toegevoegd is
                    $checkInbox = $pdo->query("SELECT * FROM klantinbox WHERE userId = $winnerId ORDER BY id DESC LIMIT 1");
                    $lastInbox = $checkInbox->fetch(PDO::FETCH_ASSOC);
                    echo "Laatste inbox melding ID: " . $lastInbox['id'] . "<br>";
                    echo "Melding: " . $lastInbox['melding'] . "<br>";

                } catch (Exception $e) {
                    echo "<span style='color:red;'>FOUT bij inbox: " . $e->getMessage() . "</span><br>";
                }

                // 5. Sluit veiling
                echo "5. Sluit veiling $auctionId<br>";
                $pdo->exec("UPDATE auctions SET status = 'closed' WHERE auctionId = $auctionId");

                echo "<strong style='color:green;'>KLAAR MET VEILING $auctionId</strong><br>";

            } else {
                echo "<span style='color:red;'>Niet genoeg pokedollars (heeft $pokedollars, nodig $bidAmount)</span><br>";
            }
        } else {
            echo "<span style='color:orange;'>Geen biedingen</span><br>";
        }

    } else {
        $tijdOver = $endTimeStamp - $currentTime;
        echo "<span style='color:orange;'>Nog $tijdOver seconden actief</span><br>";
    }

    echo "<br>";
}

echo "<hr>";
echo "<h2>Klaar met checken</h2>";
echo "<a href='../index.php?page=auctionPage'>Terug naar veilingen</a><br>";
echo "<a href='../index.php?page=auctionPage'>Refresh</a>";

// COMMENT de redirect uit voor debug
// header("Location: ../index.php?page=auctionPage");
// exit;