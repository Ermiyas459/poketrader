<?php
include "../../Private/connectionpoke.php";

$id = $_GET['card_id'];

$sql = 'DELETE FROM pokecards WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header("Location: ../index.php?page=Auctionadmin");
exit;
?>


