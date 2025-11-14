<title>Inbox | Poketrader</title>

<?php
include '../Private/connectionpoke.php';

$sql = "SELECT * FROM klantinbox where status='open'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$inbox = $stmt->fetchAll();

$userId = $_SESSION['userId'];

$sql = "SELECT * FROM klantinbox WHERE userId = :userId ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$messages = $stmt->fetchAll();
?>

<div class="header-auction">Personal Inbox
    <p class="pokedollars">
        <small><a href="index.php?page=customerdashboard">Customerdashboard</a></small>
        <br><br><br><br>

    </p>
</div>


<?php if ($messages): ?>
    <div class="user-inbox-container">
        <?php foreach ($messages as $m): ?>
            <div class="user-inbox-message">
                <div class="user-inbox-text">
                    <?= htmlspecialchars($m['melding']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="no-messages">You have no messages.</p>
<?php endif; ?>


<section id="inbox" class="inbox">
    <div class="messages-container">
        <?php if (!empty($inbox)) : ?>
            <?php foreach ($inbox as $message): ?>
                <div class="messages">
                    <form method="post" action="php/inbox.php">
                        <div class="message-info">

                            <img class="pokecards-imggg" src="<?= htmlspecialchars($message['PokecardImg']) ?>" alt="<?= htmlspecialchars($message['PokecardName']) ?>">
                            <p><span class="label">Name:</span> <?= htmlspecialchars($message['PokecardName']) ?></p>
                            <p><span class="label">Price €:</span> <?= htmlspecialchars($message['price']) ?></p>
                            <p><span class="label">Notification:</span> <?= htmlspecialchars($message['melding']) ?></p>


                        </div>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($message['id']) ?>">
                        <input type="hidden" name="PokecardName" value="<?= htmlspecialchars($message['PokecardName']) ?>">
                        <input type="hidden" name="PokecardId" value="<?= htmlspecialchars($message['PokecardId']) ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($message['price']) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($message['status']) ?>">
                        <input type="hidden" name="PokecardImg" value="<?= htmlspecialchars($message['PokecardImg']) ?>">

                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no notification(s)!</p>
        <?php endif; ?>
    </div>
</section>


