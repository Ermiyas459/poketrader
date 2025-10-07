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
    $sql = "INSERT INTO wallet (userId, pokedollars) VALUES (:userId, :amount)";
    $insert = $pdo->prepare($sql);
    $insert->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insert->bindParam(':amount', $amount, PDO::PARAM_INT);
    $insert->execute();
}


header('Location: ../index.php?page=adminInbox');
exit;
?>