<?php
Session_start();
include '../../Private/connectionpoke.php';

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


    $sql = 'INSERT INTO pokecards (name, price, description, status, img, nature)
        VALUES (:name, :price, :description, :status, :img, :nature)';
    $statement = $pdo->prepare($sql);

    $statement->BindParam('name' , $name);
    $statement->BindParam('price' , $price);
    $statement->BindParam('description' , $description);
    $statement->BindParam('status' , $status);
    $statement->BindParam('img' , $imgPath);
    $statement->BindParam('nature' , $nature);

    $statement->execute();

   header('Location: ../index.php?page=Auctionadmin');
}

?>

