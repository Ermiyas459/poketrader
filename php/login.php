<?php
session_start();
include '../../PRIVATE/connectionpoke.php';

$email = $_POST['email'];
$password = $_POST['password'];
$role = 'Klant';


$sql = 'SELECT * FROM users WHERE email = :email AND `password` = :password';
$statement = $pdo->prepare($sql);
$statement->BindParam('email' , $email);
$statement->BindParam('password' , $password);
$statement->execute();

$result = $statement->fetch(PDO::FETCH_ASSOC);

if ($result['role'] == 'Klant') {
    $_SESSION['role'] = 'Klant';
    header('Location: ../index.php?page=homepage');}
else if ($result['role'] == 'Admin') {
    $_SESSION['role'] = 'Admin';
    header('Location: ../index.php?page=admindashboard');}
else{
    $_SESSION["meldinglogin"] = "Onjuiste gebruikersnaam en/of wachtwoord";
    header('Location: ../index.php?page=login');}
?>
