<?php
include "../../Private/connectionpoke.php";

$userid = $_GET['id'];

$sql = 'DELETE FROM users WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $userid, PDO::PARAM_INT);
$stmt->execute();

header("Location: ../index.php?page=admindashboard");
exit;
?>

