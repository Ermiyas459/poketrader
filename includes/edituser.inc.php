<?php
session_start();
include "../Private/pokeconnection.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Geen gebruiker ID opgegeven.");
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Gebruiker niet gevonden.");
}

// Haal foutmeldingen/succes op uit sessie
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? "";
unset($_SESSION['errors'], $_SESSION['success']);
?>

<h1>Gebruiker bewerken</h1>

<?php if ($success): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="PHP/edituser.php">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

    <label>Naam:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>"><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"><br><br>

    <label>Wachtwoord (laat leeg om niet te wijzigen):</label><br>
    <input type="password" name="password"><br><br>

    <label>Geboortedatum:</label><br>
    <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>"><br><br>

    <label>Rol:</label><br>
    <select name="role">
        <option value="Klant" <?php if ($user['role'] === 'Klant') echo 'selected'; ?>>Klant</option>
        <option value="Admin" <?php if ($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
    </select><br><br>

    <button type="submit">Opslaan</button>
    <a href="../index.php?page=admindashboard">Annuleren</a>
</form>
