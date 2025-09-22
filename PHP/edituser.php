<?php
session_start();
include "../../Private/pokeconnection.php"; // Verbind met de database

// Reset meldingen
$_SESSION['errors'] = [];
$_SESSION['success'] = "";

// Alleen uitvoeren als het formulier is verstuurd
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $birth_date = $_POST['birth_date'];
    $role = $_POST['role'];

    $errors = [];

    // Validatie
    if (empty($name)) {
        $errors[] = "Naam is verplicht.";
    }
    if (empty($email)) {
        $errors[] = "Email is verplicht.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ongeldig e-mailadres.";
    }
    if (empty($role)) {
        $errors[] = "Rol is verplicht.";
    }

    // Controleer of email uniek is (behalve bij huidige gebruiker)
    $sql_check_email = "SELECT id FROM users WHERE email = :email AND id != :id";
    $stmt_check_email = $pdo->prepare($sql_check_email);
    $stmt_check_email->execute([':email' => $email, ':id' => $id]);

    if ($stmt_check_email->rowCount() > 0) {
        $errors[] = "Dit e-mailadres wordt al gebruikt.";
    }

    // Als er geen errors zijn → updaten
    if (empty($errors)) {
        $sql = "UPDATE users 
                SET name = :name, email = :email, birth_date = :birth_date, role = :role";

        if (!empty($password)) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':role', $role);

        if (!empty($password)) {
            $stmt->bindParam(':password', $password);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Gebruiker succesvol bijgewerkt!";
            header("Location: ../index.php?page=admindashboard");
            exit;
        } else {
            $errors[] = "Fout bij het bijwerken van de gebruiker.";
        }
    }

    $_SESSION['errors'] = $errors;
    header("Location: ../index.php?page=edituser&id=$id");
    exit;
}
?>

