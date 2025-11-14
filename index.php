<?php
session_start();

include '../Private/poketrader_db.connection.php';

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];

    $sql = 'SELECT * FROM users WHERE userId = :userId';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['roleName'] = $result['role']; // role staat nu direct in users
    }
}

if (isset($_SESSION['roleName'])) {
    if ($_SESSION['roleName'] == 'klant') {
        $navbar = 'inc/navbar-klant.inc.php';
    } else if ($_SESSION['roleName'] == 'admin') {
        $navbar = 'inc/navbar-admin.inc.php';
    } else {
        $navbar = 'inc/navbar-Index.inc.php';
    }
} else {
    $navbar = 'inc/navbar-Index.inc.php';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBuddy</title>
    <link rel="stylesheet" href="css/stylesheet.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<?php include $navbar; ?>

<?php
$page = $_GET['page'] ?? 'homepage';
include 'inc/' . $page . '.inc.php';
?>

</body>
</html>
