<?php
session_start();
include '../Private/poketrader_db.connection.php';

// User must be logged in
if (!isset($_SESSION['userId']) || (int)$_SESSION['userId'] <= 0) {
    header('Location: ../index.php?page=login');
    exit;
}

// Only accept POST requests from the form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=pokedollarpage');
    exit;
}

// Get data from form
$userId   = (int)$_SESSION['userId']; // trust session, not hidden input
$coins    = isset($_POST['coins']) ? (int)$_POST['coins'] : 0;       // Poké$
$price    = isset($_POST['price']) ? (float)$_POST['price'] : 0;     // €
$packName = isset($_POST['packName']) ? trim($_POST['packName']) : '';

// Basic validation
if ($coins <= 0 || $price <= 0 || $packName === '') {
    header('Location: ../index.php?page=pokedollarpage&error=invalid');
    exit;
}

try {
    // Insert request into inbox table
    $sql = "INSERT INTO inbox (name, amount, userId, status) 
            VALUES (:name, :amount, :userId, 'waiting')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name'   => $packName,
        'amount' => $coins,
        'userId' => $userId
    ]);

    // Redirect back to the Poké$ shop with success
    header('Location: ../index.php?page=pokedollarpage&request=ok');
    exit;

} catch (PDOException $e) {
    // Optional: log error
    // error_log($e->getMessage());
    header('Location: ../index.php?page=pokedollarpage&error=db');
    exit;
}

