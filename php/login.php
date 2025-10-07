<?php
session_start();
include '../../Private/poketrader_db.connection.php';

$email = $_POST['email'];
$password = $_POST['password'];


$sql = 'SELECT * FROM users WHERE email = :email';
$statement = $pdo->prepare($sql);
$statement->bindParam(':email', $email);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);


if ($result && password_verify($password, $result['password'])) {
    $_SESSION['userId'] = $result['userId'];
    $_SESSION['role']   = $result['role'];

    if ($result['role'] == 'Klant') {
        header('Location: ../index.php?page=costumerdashboard');
    } else if ($result['role'] == 'admin') {
        header('Location: ../index.php?page=admindashboard');
    }
    exit();
} else {
    $_SESSION["meldinglogin"] = "Onjuiste gebruikersnaam en/of wachtwoord";
    header('Location: ../index.php?page=login');
    exit();
}
?>
