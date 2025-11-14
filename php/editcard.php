<?php
include '../../Private/connectionpoke.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)($_POST['id'] ?? 0);
    $name      = $_POST['name'] ?? '';
    $price     = $_POST['price'] ?? '';
    $status    = $_POST['status'] ?? '';
    $nature    = $_POST['nature'] ?? '';
    $desc      = $_POST['description'] ?? '';

    // Afbeelding: alleen updaten als er een nieuwe is geüpload
    $imgPath = null;
    if (!empty($_FILES['img']['name'])) {
        // uploaden naar map, bijv. 'uploads/...'
        // en $imgPath instellen
    }

    if ($imgPath) {
        $sql = "UPDATE pokecards
                SET name = :name,
                    price = :price,
                    status = :status,
                    nature = :nature,
                    description = :description,
                    img = :img
                WHERE id = :id";
        $params = [
            ':name' => $name,
            ':price' => $price,
            ':status' => $status,
            ':nature' => $nature,
            ':description' => $desc,
            ':img' => $imgPath,
            ':id' => $id,
        ];
    } else {
        $sql = "UPDATE pokecards
                SET name = :name,
                    price = :price,
                    status = :status,
                    nature = :nature,
                    description = :description
                WHERE id = :id";
        $params = [
            ':name' => $name,
            ':price' => $price,
            ':status' => $status,
            ':nature' => $nature,
            ':description' => $desc,
            ':id' => $id,
        ];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header("Location: ../index.php?page=Auctionadmin&msg=Card+updated");
    exit;
}
