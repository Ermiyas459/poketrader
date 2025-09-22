<?php
try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=poketrader', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Databaseverbinding mislukt: " . $error->getMessage());
}
?>