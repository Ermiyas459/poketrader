<?php
session_start();
include '../../Private/connectionpoke.php';

// only admins
if (!isset($_SESSION['role']) || $_SESSION['role'] === "Klant") {
    unset($_SESSION["role"]);
    header("Location: ../index.php?page=404");
    exit;
}

if (isset($_POST['acceptBTN'])) {

    $messageId = isset($_POST['messageId']) ? (int)$_POST['messageId'] : 0;

    if ($messageId <= 0) {
        header('Location: ../index.php?page=adminInbox&msg=invalid_id');
        exit;
    }

    // Load original request info
    $sql = "SELECT * FROM inbox WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $messageId]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$msg) {
        header('Location: ../index.php?page=adminInbox&msg=not_found');
        exit;
    }

    $userId   = (int)$msg['userId'];
    $amount   = (int)$msg['amount'];      // Poké$
    $price    = (float)$msg['price'];     // euro
    $packName = $msg['pokecardsName'];

    // 1. Add Poké$ to wallet
    $sqlWallet = "INSERT INTO wallet (userId, pokedollars, created_at)
                  VALUES (:userId, :pokedollars, NOW())";
    $stmtWallet = $pdo->prepare($sqlWallet);
    $stmtWallet->execute([
        'userId'      => $userId,
        'pokedollars' => $amount
    ]);

    // 2. Mark the admin request as accepted
    $sqlUpdate = "UPDATE inbox SET status = 'accepted' WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute(['id' => $messageId]);

    // 3. Create message in the USER inbox
    $sqlUserInbox = "INSERT INTO klantinbox
                        (melding, PokecardId, price, userId, status, PokecardImg, PokecardName)
                     VALUES
                        (:melding, :PokecardId, :price, :userId, :status, :PokecardImg, :PokecardName)";
    $stmtUserInbox = $pdo->prepare($sqlUserInbox);
    $stmtUserInbox->execute([
        'melding'      => 'Your Poké$ request for ' . $amount . ' Poké$ (' . $packName . ') has been accepted.',
        'PokecardId'   => 0,
        'price'        => $price,
        'userId'       => $userId,
        'status'       => 'new',        // or 'unread'
        'PokecardImg'  => '',           // you can fill an image url if you want
        'PokecardName' => $packName
    ]);

    header('Location: ../index.php?page=adminInbox&msg=accepted');
    exit;
}
