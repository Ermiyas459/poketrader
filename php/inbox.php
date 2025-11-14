<?php
include '../Private/poketrader_db.connection.php';


$winnerId = $winner['id'];
$PokecardName = $auction['PokecardName'];
$PokecardImg = $auction['PokecardImg'];
$price = $auction['highestBid'];

try {
    $pdo->beginTransaction();

    // geld afhalen bij winnaar
    $sql = "UPDATE wallet SET pokedollars = pokedollars - :price WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':userId', $winnerId);
    $stmt->execute();

    // melding toevoegen
    $melding = "Gefeliciteerd! Je hebt de veiling gewonnen voor $PokecardName voor $price pokedollars!";
    $sql = "INSERT INTO klantInbox (userId, melding, PokecardName, PokecardImg, price, status, created_at)
            VALUES (:userId, :melding, :PokecardName, :PokecardImg, :price, 'unseen', NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $winnerId);
    $stmt->bindParam(':melding', $melding);
    $stmt->bindParam(':PokecardName', $PokecardName);
    $stmt->bindParam(':PokecardImg', $PokecardImg);
    $stmt->bindParam(':price', $price);
    $stmt->execute();

    $pdo->commit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Fout: " . htmlspecialchars($e->getMessage());
}
?>
