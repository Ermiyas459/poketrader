<?php
include '../Private/poketrader_db.connection.php';
$sql = "SELECT * FROM klantInbox where status='open'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$inbox = $stmt->fetchAll();
?>
<section id="inbox" class="inbox">
    <div class="messages-container">
        <?php if (!empty($inbox)) : ?>
            <?php foreach ($inbox as $message): ?>
                <div class="messages">
                    <form method="post" action="php/inbox.php">
                        <div class="message-info">

                            <img class="pokecards-img" src="<?= htmlspecialchars($message['PokecardImg']) ?>" alt="<?= htmlspecialchars($message['PokecardName']) ?>">
                            <p><span class="label">name:</span> <?= htmlspecialchars($message['PokecardName']) ?></p>
                            <p><span class="label">price:</span> <?= htmlspecialchars($message['price']) ?></p>
                            <p><span class="label">melding:</span> <?= htmlspecialchars($message['melding']) ?></p>
                            <p><span class="label">Status:</span> <?= htmlspecialchars($message['status']) ?></p>


                        </div>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($message['id']) ?>">
                        <input type="hidden" name="PokecardName" value="<?= htmlspecialchars($message['PokecardName']) ?>">
                        <input type="hidden" name="PokecardId" value="<?= htmlspecialchars($message['PokecardId']) ?>">
                        <input type="hidden" name="senderId" value="<?= htmlspecialchars($message['senderId']) ?>">
                        <input type="hidden" name="receiverId" value="<?= htmlspecialchars($message['receiverId']) ?>">
                        <input type="hidden" name="price" value="<?= htmlspecialchars($message['price']) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($message['status']) ?>">
                        <input type="hidden" name="PokecardImg" value="<?= htmlspecialchars($message['PokecardImg']) ?>">


                        <button type="submit" name="acceptBTN">Accepteren</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Je hebt geen meldingen!</p>
        <?php endif; ?>
    </div>
</section>
