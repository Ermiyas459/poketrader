<?php
session_start();
include '../../Private/connectionpoke.php';

$PokecardId  = $_POST['PokecardId'];
$senderId = $_POST['senderId'];
$receiverId = $_POST['receiverId'];
$status = $_POST['status'];
$PokecardImg  = $_POST['PokecardImg'];
$PokecardName = $_POST['PokecardName'];
$id = $_POST['id'];
$price = $_POST['price'];

try {

    if ($PokecardId <= 0 || $senderId <= 0 || $receiverId <= 0 || $id <= 0 || $price <= 0) {
        throw new Exception("Ongeldige gegevens ontvangen.");
    }


    $pdo->beginTransaction();


    $sql = "SELECT pokedollars FROM wallet WHERE userId = :userId FOR UPDATE";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $senderId, PDO::PARAM_INT);
    $stmt->execute();
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet || $wallet['pokedollars'] < $price) {
        $pdo->rollBack();
        throw new Exception("De koper heeft niet genoeg pokedollars. Transactie geannuleerd.");
    }


    $sql = "UPDATE wallet SET pokedollars = pokedollars - :price WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':userId', $senderId, PDO::PARAM_INT);
    $stmt->execute();


    $sql = "INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :amount)
            ON DUPLICATE KEY UPDATE pokedollars = pokedollars + :amount";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $receiverId, PDO::PARAM_INT);
    $stmt->bindParam(':amount', $price);
    $stmt->execute();


    $sql = 'UPDATE pokecards SET userId = :userId WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $senderId);
    $stmt->bindParam(':id', $PokecardId);
    $stmt->execute();


    $sql = 'UPDATE klantinbox SET status = "closed" WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();


    $pdo->commit();

    header("Location: ../index.php?page=inbox");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<p style='color:red;'>Fout: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>
