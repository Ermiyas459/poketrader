<?php
session_start();
include '../Private/poketrader_db.connection.php';

   $status = $_POST['status'];
   $userId = $_POST['userId'];
   $amount = $_POST['amount'];
   $name = $_POST['name'];

$sql = "UPDATE inbox SET status = 'accepted' WHERE userId = :userId";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userId', $userId);


if ($stmt->execute()) {

    $sqlCheck = "SELECT pokedollars FROM wallet WHERE userId = :userId";
    $check = $pdo->prepare($sqlCheck);
    $check->bindParam(':userId', $userId, PDO::PARAM_INT);
    $check->execute();

    if ($check->rowCount() > 0) {

        $sqlUpdate = "UPDATE wallet SET pokedollars = pokedollars + :amount WHERE userId = :userId";
        $update = $pdo->prepare($sqlUpdate);
        $update->bindParam(':amount', $amount, PDO::PARAM_INT);
        $update->bindParam(':userId', $userId, PDO::PARAM_INT);
        $update->execute();
    } else {

        $sqlInsert = "INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :amount)";
        $insert = $pdo->prepare($sqlInsert);
        $insert->bindParam(':userId', $userId, PDO::PARAM_INT);
        $insert->bindParam(':amount', $amount, PDO::PARAM_INT);
        $insert->execute();
    }
}

header('Location: ../index.php?page=adminInbox');
exit;
?>