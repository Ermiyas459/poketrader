<?php
session_start();
include '../Private/poketrader_db.connection.php';

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE klantinbox SET status = 'closed' WHERE id = :id ";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
header("Location: ../index.php?page=inbox");
exit;





?>

