<?php
include '../Private/poketrader_db.connection.php';


for ($i = 1; $i <= 20; $i++) {
    $data = file_get_contents("https://pokeapi.co/api/v2/pokemon/$i");
    $pokemon = json_decode($data, true);

    $name = ucfirst($pokemon['name']);
    $type = $pokemon['types'][0]['type']['name'];
    $img = $pokemon['sprites']['front_default'];
    $price = rand(100, 500);
    $status = 'available';
    $userId = NULL;


    $check = $conn->prepare("SELECT id FROM pokecards WHERE name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO pokecards (name, price, type, status, userId, img) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissi", $name, $price, $type, $status, $userId, $img);
        $stmt->execute();
    }
}

echo "✅ Pokémon import succesvol!";
