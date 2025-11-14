<title>Inbox | ADMIN</title>
<?php
include '../Private/connectionpoke.php';

if (!isset($_SESSION['role']) || $_SESSION ['role'] == "Klant") {
    unset($_SESSION["role"]);
    header("Location: index.php?page=404");
    exit;
}

$sql = "SELECT * FROM inbox where status='waiting'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$inbox = $stmt->fetchAll();

?>

<div class="header-auction">Admin Inbox
    <p class="pokedollars">
        <small><a href="index.php?page=Admindashboard">Admindashboard</a></small>
        <br><br><br><br>

    </p>
</div>

<section id="inbox" class="inbox">
    <div class="messages-container">
        <?php if (!empty($inbox)) : ?>
            <?php foreach ($inbox as $message): ?>
                <div class="messages">
                    <form method="post" action="php/admininbox.php">
                        <div class="message-info">
                            <p><span class="label">Pack:</span> <?= htmlspecialchars($message['pokecardsName']) ?></p>
                            <p><span class="label">Poké$:</span> <?= htmlspecialchars($message['amount']) ?></p>
                            <p><span class="label">Status:</span> <?= htmlspecialchars($message['status']) ?></p>
                        </div>

                        <input type="hidden" name="messageId" value="<?= (int)$message['id'] ?>">

                        <button type="submit" name="acceptBTN">Accept</button>
                    </form>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <p>You have no notification(s)!</p>
        <?php endif; ?>
    </div>
</section>


