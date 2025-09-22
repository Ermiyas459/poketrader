<?php
session_start();
include '../Private/poketrader_db.connection.php';


$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirmpassword'];
$birth_date = $_POST['birth_date'];
$role = 'Klant';


if ($password !== $confirmpassword) {
    $_SESSION["meldingpsw"] = "Wachtwoorden komen niet overeen.";
    header('Location: ../index.php?page=registration');
    exit;
}

$pattern = "/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'\"\\\\|,.<>\/?]).{8,}$/";

if (!preg_match($pattern, $password)) {
    $_SESSION['meldingpsw'] = "Wachtwoord moet minimaal 8 tekens bevatten, met minstens één hoofdletter, één kleine letter, één cijfer en één speciaal teken.";
    header('Location: ../index.php?page=registration');
    exit;
}

$checkSql = "SELECT COUNT(*) FROM users WHERE email = :email";
$checkStmt = $pdo->prepare($checkSql);
$checkStmt->bindParam(':email', $email);
$checkStmt->execute();


if ($checkStmt->fetchColumn() > 0) {
    $_SESSION["meldingpsw"] = "Dit e-mailadres is al in gebruik.";
    header('Location: ../index.php?page=registration');
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = 'INSERT INTO users (userId, name, email, password, birth_date, role) VALUES (NULL, :name, :email, :password, :birth_date, :role)';
$statement = $pdo->prepare($sql);
$statement->bindParam(':name', $name);
$statement->bindParam(':email', $email);
$statement->bindParam(':password', $hashedPassword);
$statement->bindParam(':birthdate', $birth_date);
$statement->bindParam(':role', $role);
$statement->execute();

header('Location: ../index.php?page=login');
$_SESSION['meldinglogin'] = "Account aangemaakt! U kunt nu inloggen.";
?>
