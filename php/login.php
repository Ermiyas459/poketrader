<?php
session_start();
include '../../PRIVATE/connectionpoke.php';

$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];


$sql = 'SELECT * FROM users WHERE email = :email AND `password` = :password';
$statement = $pdo->prepare($sql);
$statement->BindParam('email' , $email);
$statement->BindParam('password' , $password);
$statement->execute();

$result = $statement->fetch(PDO::FETCH_ASSOC);
$_SESSION['id'] = $result['id'];
$_SESSION['role'] = $role;

if ($result['role'] == 'Klant') {
    $_SESSION['role'] = 'Klant';
    header('Location: ../index.php?page=customerdashboard');}
else if ($result['role'] == 'Admin') {
    $_SESSION['role'] = 'Admin';
    header('Location: ../index.php?page=admindashboard');}
else{
    $_SESSION["meldinglogin"] = "Onjuiste gebruikersnaam en/of wachtwoord";
    header('Location: ../index.php?page=login');}
?>
