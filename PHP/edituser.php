<?php
session_start();
include "../../Private/pokeconnection.php";

$_SESSION['errors'] = [];
$_SESSION['success'] = "";

// Controleer eerst of er een POST is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Controleer of userid bestaat in POST
    if (!isset($_POST['userId'])) {
        $_SESSION['errors'][] = "Geen gebruikers-ID ontvangen.";
        header("Location: ../index.php?page=admindashboard");
        exit;
    }

    $id = $_POST['userId'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $birth_date = $_POST['birth_date'];
    $role = $_POST['role'];

    $errors = [];

    if (empty($name)) $errors[] = "Naam is verplicht.";
    if (empty($email)) $errors[] = "Email is verplicht.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Ongeldig e-mailadres.";
    if (empty($role)) $errors[] = "Rol is verplicht.";

    // Controleer of e-mail uniek is
    $sql_check_email = "SELECT userId FROM users WHERE email = :email  AND userId != :userId";
    $stmt_check_email = $pdo->prepare($sql_check_email);
    $stmt_check_email->execute([':email' => $email, ':userId' => $id]);
    if ($stmt_check_email->rowCount() > 0) {
        $errors[] = "Dit e-mailadres wordt al gebruikt.";
    }

    if (empty($errors)) {
        $sql = "UPDATE users 
                SET name = :name, email = :email, birth_date = :birth_date, role = :role";

        if (!empty($password)) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE userId = :userId";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':role', $role);

        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed);
        }

        if ($stmt->execute()) {

            header("Location: ../index.php?page=admindashboard");
            exit;
        } else {
            $errors[] = "Fout bij het bijwerken van de gebruiker.";
        }
    }

    $_SESSION['errors'] = $errors;
    header("Location: ../index.php?page=edituser&userId=$id");
    exit;
}
?>
