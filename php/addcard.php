<?php
Session_start();
include '../Private/poketrader_db.connection.php';


$name = $_POST['name'];
$price = $_POST['price'];
$description = $_POST['description'];
$status = $_POST['status'];
$nature = $_POST['nature'];
$imgFile = $_FILES['img'] ?? null;


if ($price <= 0) {
    echo "Het bedrag moet groter zijn dan 0!";
}

else {

    $uploadDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext       = strtolower(pathinfo($imgFile['name'], PATHINFO_EXTENSION));
    $filename  = bin2hex(random_bytes(8)) . ($ext ? ".$ext" : '');
    $target    = $uploadDir . '/' . $filename;

    if (!move_uploaded_file($imgFile['tmp_name'], $target)) {
        echo 'Fout bij het opslaan van de afbeelding.';
        exit;
    }

// Dit is wat we in de DB opslaan (STRING!)
    $imgPath = 'uploads/' . $filename;


    $createdAt = date('Y-m-d H:i:s');

    $sql = 'INSERT INTO pokecards (name, price, description, status, img, nature, created_at)
        VALUES (:name, :price, :description, :status, :img, :nature, :created_at)';
    $statement = $pdo->prepare($sql);

    $statement->bindParam(':name', $name);
    $statement->bindParam(':price', $price);
    $statement->bindParam(':description', $description);
    $statement->bindParam(':status', $status);
    $statement->bindParam(':img', $imgPath);
    $statement->bindParam(':nature', $nature);
    $statement->bindParam(':created_at', $createdAt);
    $statement->execute();

    header('Location: ../index.php?page=Auctionadmin');
}

?>

