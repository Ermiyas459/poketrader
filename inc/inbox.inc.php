<?php
include '../Private/poketrader_db.connection.php';
$sql = "SELECT * FROM klantInbox where status='open'and userId='".$_SESSION['userId']."'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$inbox = $stmt->fetchAll();
?>
<section id="inbox" class="inbox">
    <div class="messages-container">
        <?php if (!empty($inbox)) : ?>
            <?php foreach ($inbox as $message): ?>

                <div class="messages">

                    <div class="message-info">
                        <form method="post" action="php/refuseKlant.php">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($message['id']) ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($message['status']) ?>">
                            <button type="submit" class="refuseBTN" name="refuseBTN"><img class="refuseBTN1" src="img/close.png"></button>


                        </form>
                        <img class="pokecards-img" src="<?= htmlspecialchars($message['PokecardImg']) ?>" alt="<?= htmlspecialchars($message['PokecardName']) ?>">
                        <p><strong><?= htmlspecialchars($message['melding']) ?></strong></p>
                        <p>Kaart: <?= htmlspecialchars($message['PokecardName']) ?></p>
                        <p>Prijs: <?= htmlspecialchars($message['price']) ?> pokedollars</p>
                        <p>Status: <?= htmlspecialchars($message['status']) ?></p>
                    </div>

                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>Je hebt geen meldingen!</p>
        <?php endif; ?>
    </div>
</section>

