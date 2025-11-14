<?php
include '../Private/poketrader_db.connection.php';
$sql = "SELECT * FROM inbox where status='waiting'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$inbox = $stmt->fetchAll();
?>
<section id="inbox" class="inbox">
    <div class="messages-container">
        <?php if (!empty($inbox)) : ?>
            <?php foreach ($inbox as $message): ?>
                <div class="messages">
                    <form method="post" action="php/adminInbox.php">
                        <div class="message-info">
                            <p><span class="label">Naam:</span> <?= htmlspecialchars($message['name']) ?></p>
                            <p><span class="label">amount:</span> <?= htmlspecialchars($message['amount']) ?></p>
                            <p><span class="label">Status:</span> <?= htmlspecialchars($message['status']) ?></p>
                        </div>
                        <input type="hidden" name="userId" value="<?= htmlspecialchars($message['userId']) ?>">
                        <input type="hidden" name="amount" value="<?= htmlspecialchars($message['amount']) ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($message['name']) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($message['status']) ?>">
                        <button type="submit" class="acceptBTN" name="acceptBTN">Accepteren</button>
                        <br>
                    </form>

                    <form method="post" action="php/refuseAdmin.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($message['id']) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($message['status']) ?>">
                        <button type="submit" class="refuseBTN" name="refuseBTN">Weigeren</button>

                    </form>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p>Je hebt geen meldingen!</p>
        <?php endif; ?>
    </div>
</section>

