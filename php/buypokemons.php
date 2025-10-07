<?php
session_start();
include '../Private/poketrader_db.connection.php';

if (!isset($_SESSION['userId'])) {
    die("Je moet inloggen om een Pokémon te kopen!");
}

$userId = $_SESSION['userId'];

if (isset($_POST['name'], $_POST['price'], $_POST['type'], $_POST['img'])) {
    $name = $_POST['name'];
    $price = (int) $_POST['price'];
    $type = $_POST['type'];
    $img = $_POST['img'];

    // Check wallet
    $sql = "SELECT pokedollars FROM wallet WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $wallet = $stmt->fetch();

    if ($wallet && $wallet['pokedollars'] >= $price) {
        $newBalance = $wallet['pokedollars'] - $price;
        $update = $pdo->prepare("UPDATE wallet SET pokedollars = :new WHERE userId = :uid");
        $update->execute(['new' => $newBalance, 'uid' => $userId]);

        // Voeg Pokémon toe
        $insert = $pdo->prepare("INSERT INTO pokecards (name, price, type, status, userId, img)
                                 VALUES (:n, :p, :t, 'owned', :u, :i)");
        $insert->execute([
            'n' => $name,
            'p' => $price,
            't' => $type,
            'u' => $userId,
            'i' => $img
        ]);

        echo "<script>alert('✅ Je hebt $name gekocht voor $price Pokedollars!');
              window.location.href='buyPokemons.php';</script>";
    } else {
        echo "<script>alert('❌ Niet genoeg Pokedollars!'); 
              window.location.href='buyPokemons.php';</script>";
    }
} else {
    echo "<script>alert('❌ Ongeldige aankoop!'); 
          window.location.href='buyPokemons.php';</script>";
}
?>
