<?php
include "../../Private/pokeconnection.php";

$userId = $_GET['userId'] ?? null;

if (!$userId) {
    die("Geen userId opgegeven.");
}

$sql = 'DELETE FROM users WHERE userId = :userId';
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: ../index.php?page=admindashboard");
    exit;
} else {
    die("Verwijderen mislukt.");
}
?>


