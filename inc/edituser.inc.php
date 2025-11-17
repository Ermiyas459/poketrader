<?php
include '../Private/poketrader_db.connection.php';




$stmt = $pdo->prepare("SELECT * FROM users WHERE userId = :userId");
$stmt->execute([':userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Gebruiker niet gevonden.");
}

// Start sessie voor meldingen
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? "";
unset($_SESSION['errors'], $_SESSION['success']);
?>

<!-- Koppel CSS -->
<link rel="stylesheet" href="css/edituser.css">
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
<link href="https://fonts.cdnfonts.com/css/pokemon-solid" rel="stylesheet">

<div class="edituser-container">
    <h1 class="edituser-title">👤 Gebruiker bewerken</h1>

    <?php if ($success): ?>
        <p class="edituser-success"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul class="edituser-errors">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="edituser-form" method="POST" action="PHP/edituser.php">
        <input type="hidden" name="userId" value="<?= htmlspecialchars($user['userId']); ?>">

        <div class="form-group">
            <label for="name">Naam:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>">
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>">
        </div>


        <div class="form-group">
            <label for="role">Rol:</label>
            <select id="role" name="role">
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Gebruiker</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Beheerder</option>
            </select>
        </div>

        <button type="submit" class="edituser-btn">💾 Opslaan</button>
        <a href="index.php?page=admindashboard" class="edituser-cancel">Annuleren</a>
    </form>
</div>