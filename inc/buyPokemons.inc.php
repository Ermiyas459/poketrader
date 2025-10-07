<?php
include '../Private/poketrader_db.connection.php';

if (!isset($_SESSION['userId'])) {
    die("Je moet inloggen om Pokémon te kopen!");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Koop Pokémon</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
        h2 { text-align: center; }
        .pokecards-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        .pokecards-content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            padding: 15px;
            width: 160px;
        }
        .pokecards-img { width: 100px; height: 100px; object-fit: contain; }
        .buy-btn {
            background-color: #ffcc01;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.2s;
        }
        .buy-btn:hover { background-color: #e6b800; }
        .back-btn {
            display: block;
            text-align: center;
            margin: 20px auto;
            width: 180px;
            text-decoration: none;
            background: #4285f4;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
        }
        .back-btn:hover { background: #357ae8; }
    </style>
</head>
<body>

<h2>🛒 Koop Pokémon</h2>
<div class="pokecards-grid">
    <?php
    // Haal 20 pokemons uit de API
    $url = "https://pokeapi.co/api/v2/pokemon?limit=20";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    foreach ($data['results'] as $pokemon) {
        $pokeDetails = json_decode(file_get_contents($pokemon['url']), true);
        $name = ucfirst($pokeDetails['name']);
        $img = $pokeDetails['sprites']['front_default'];
        $type = ucfirst($pokeDetails['types'][0]['type']['name']);
        $price = rand(50, 500);
        ?>

        <div class="pokecards-content">
            <img class="pokecards-img" src="<?= $img ?>" alt="<?= $name ?>">
            <p><b><?= $name ?></b></p>
            <p>Type: <?= $type ?></p>
            <p>💰 <?= $price ?> Pokedollars</p>
            <form action="./php/buypokemons.php" method="POST">
                <input type="hidden" name="name" value="<?= $name ?>">
                <input type="hidden" name="price" value="<?= $price ?>">
                <input type="hidden" name="type" value="<?= $type ?>">
                <input type="hidden" name="img" value="<?= $img ?>">
                <button type="submit" class="buy-btn">Koop</button>
            </form>
        </div>

    <?php } ?>
</div>

<a href="customer_dashboard.php" class="back-btn">⬅️ Terug naar Dashboard</a>

</body>
</html>
