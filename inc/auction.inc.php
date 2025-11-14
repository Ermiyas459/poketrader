<?php
include '../Private/connectionpoke.php'; // pad evt. aanpassen

// Get logged-in user ID (if any)
$userId = $_SESSION['userId'] ?? 0;

$userName = "Currently not logged in";
$pokedollars = 0;

// If user is logged in, get their name and balance
if ($userId > 0) {
    // Get user name
    $sqlUser = "SELECT name FROM users WHERE id = :userId";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute(['userId' => $userId]);
    $user = $stmtUser->fetch();

    if ($user) {
        $userName = htmlspecialchars($user['name']);
    }

    // Get pokedollar balance
    $sql = "SELECT SUM(pokedollars) AS pokedollars 
            FROM wallet 
            WHERE userId = :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    $wallet = $stmt->fetch();
    $pokedollars = $wallet['pokedollars'] ?? 0;
}

// Alle kaarten uit de database halen (nieuwste eerst)
$sql = "SELECT * FROM pokecards ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$globalMessage = $_GET['msg'] ?? '';
?>


<title>Auction | Poketrader</title>

<body class="home-page">

<div class="header-auction">Pokémon Auction
    <p class="pokedollars">
        <small><a href="index.php?page=customerdashboard">Customerdashboard</a></small>
        <br><br><br><br>
        <small>current Poké$ balance</small><br>
        <span id="balance"><?= htmlspecialchars($pokedollars) ?></span>
        <br><br><br><br>
        <small><?= $userName ?></small>
    </p>
</div>

<?php if ($globalMessage): ?>
    <p style="text-align:center; font-weight:bold;">
        <?= htmlspecialchars($globalMessage) ?>
    </p>
<?php endif; ?>

<div class="auction-wrapper">

    <?php if (empty($cards)): ?>
        <p>There are currently no cards available.</p>
    <?php else: ?>

        <?php foreach ($cards as $card): ?>
            <?php
            $startPrice = (float)$card['price'];

            // Veiling duurt 10 min = 600 sec
            $totalSeconds = 600;

            $createdAt = new DateTime($card['created_at']);
            $now       = new DateTime();
            $diff      = $now->getTimestamp() - $createdAt->getTimestamp();

            $remaining = $totalSeconds - $diff;
            if ($remaining < 0) {
                $remaining = 0;
            }
            $auctionActive = $remaining > 0;

            // Hoogste bod uit bids (indien aanwezig) + user_id
            $stmtHighest = $pdo->prepare(
                'SELECT b.amount, b.user_id, u.name 
         FROM bids b
         JOIN users u ON u.id = b.user_id
         WHERE b.card_id = :card_id
         ORDER BY b.amount DESC, b.created_at ASC
         LIMIT 1'
            );
            $stmtHighest->execute([':card_id' => $card['id']]);
            $highestRow = $stmtHighest->fetch(PDO::FETCH_ASSOC);

            $highestBid        = $startPrice;
            $highestBidderName = null;
            if ($highestRow) {
                $highestBid        = max($startPrice, (float)$highestRow['amount']);
                $highestBidderName = $highestRow['name'];
            }

            $winnerUserId = $card['winner_user_id'] ?? null;

            // 👉 ALS VEILING KLAAR IS: winner bepalen + kaart NIET tonen
            if (!$auctionActive) {
                if (empty($winnerUserId) && $highestRow) {
                    try {
                        $pdo->beginTransaction();

                        $winnerUserId = (int)$highestRow['user_id'];

                        // 1. Winner opslaan bij de kaart
                        $stmtSetWinner = $pdo->prepare(
                            'UPDATE pokecards 
                     SET winner_user_id = :winner 
                     WHERE id = :card_id'
                        );
                        $stmtSetWinner->execute([
                            ':winner'  => $winnerUserId,
                            ':card_id' => $card['id'],
                        ]);

                        // 2. Kaart in collectie van de winnaar plaatsen
                        $stmtCollection = $pdo->prepare(
                            'INSERT INTO collection (user_id, card_id)
                     VALUES (:user_id, :card_id)'
                        );
                        $stmtCollection->execute([
                            ':user_id' => $winnerUserId,
                            ':card_id' => $card['id'],
                        ]);

// 3. Bericht in de inbox van de winnaar
                        $meldingTekst = "Congratulations! You have won: "
                            . $card['name'] . " for €" . number_format($highestBid, 2, ',', '.');

                        $stmtInbox = $pdo->prepare(
                            'INSERT INTO klantinbox 
        (melding, PokecardId, price, userId, status, PokecardImg, PokecardName)
     VALUES 
        (:melding, :PokecardId, :price, :userId, :status, :PokecardImg, :PokecardName)'
                        );

                        $stmtInbox->execute([
                            ':melding'      => $meldingTekst,
                            ':PokecardId'   => $card['id'],
                            ':price'        => $highestBid,
                            ':userId'       => $winnerUserId,        // of weg laten als je die kolom niet hebt
                            ':status'       => 'open',
                            ':PokecardImg'  => $card['img'],
                            ':PokecardName' => $card['name'],
                        ]);



                        $pdo->commit();
                    } catch (Exception $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        // eventueel loggen
                    }
                }

                // ❌ deze kaart niet meer tonen op de auction-pagina
                continue;
            }

            // Vanaf hier: alleen nog ACTIEVE veilingen
            // Laatste 5 bids
            $stmtHistory = $pdo->prepare(
                'SELECT b.amount, u.name, b.created_at
         FROM bids b
         JOIN users u ON u.id = b.user_id
         WHERE b.card_id = :card_id
         ORDER BY b.created_at DESC
         LIMIT 5'
            );
            $stmtHistory->execute([':card_id' => $card['id']]);
            $history = $stmtHistory->fetchAll(PDO::FETCH_ASSOC);

            $canBid = $userId > 0; // auctionActive is hier sowieso true
            ?>

            <div class="auction-card" data-card-id="<?php echo (int)$card['id']; ?>">
                <!-- Image -->
                <div class="card-image">
                    <?php if (!empty($card['img'])): ?>
                        <img src="<?php echo htmlspecialchars($card['img']); ?>" alt="Pokecard">
                    <?php else: ?>
                        No picture
                    <?php endif; ?>
                </div>

                <!-- Gegevens -->
                <div class="card-info">
                    <h2>Info Pokecard</h2>
                    <ul>
                        <li><span>Name:</span>
                            <?php echo htmlspecialchars($card['name']); ?>
                        </li>
                        <li><span>Starting Price:</span>
                            €<?php echo number_format($startPrice, 2, ',', '.'); ?>
                        </li>
                        <li><span>HP Pokemon:</span>
                            <?php echo htmlspecialchars($card['status']); ?>
                        </li>
                        <li><span>Nature:</span>
                            <?php echo htmlspecialchars($card['nature']); ?>
                        </li>
                        <li><span>Description:</span>
                            <?php echo nl2br(htmlspecialchars($card['description'])); ?>
                        </li>
                    </ul>
                </div>

                <!-- Rechts: bied-info + TIMER -->
                <div class="right-panel">
                    <div class="highest-bid">
                        Highest bid: €<span class="highestBid">
                    <?php echo number_format($highestBid, 0, ',', ''); ?>
                </span>
                    </div>
                    <?php if ($highestBidderName): ?>
                        <div>
                            Who bid: <?= htmlspecialchars($highestBidderName) ?>
                        </div>
                    <?php endif; ?>

                    <div>Starting Price: €<?php echo number_format($startPrice, 2, ',', '.'); ?></div>

                    <div class="timer" data-remaining="<?php echo (int)$remaining; ?>">
                        Auction ends in:
                        <span class="time-text"></span>
                    </div>

                    <div class="bid-history">
                        <strong>Last bids:</strong><br>
                        <?php if (empty($history)): ?>
                            (No bids yet)
                        <?php else: ?>
                            <?php foreach ($history as $bid): ?>
                                €<?php echo number_format($bid['amount'], 2, ',', '.'); ?>
                                - <?= htmlspecialchars($bid['name']) ?><br>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>



                </div>
            </div>

            <!-- Biedsectie per kaart (POST naar bid.php) -->
            <form class="bid-section" action="php/bid.php" method="post">
                <input type="hidden" name="card_id" value="<?php echo (int)$card['id']; ?>">

                <input type="number"
                       name="amount"
                       class="bidInput"
                       placeholder="Type your bid here"
                       step="1"
                       min="0"
                    <?= $canBid ? '' : 'disabled' ?>>

                <button type="submit"
                        class="bidButton"
                    <?= $canBid ? '' : 'disabled' ?>>
                    <?= $userId > 0 ? 'Bid' : 'You have to log in' ?>
                </button>
            </form>

            <div class="message"></div>

            <hr>
        <?php endforeach; ?>


    <?php endif; ?>

</div>

<!-- Balance naar JS (alleen voor clientside check) -->
<script>
    const userBalance = <?= (int)$pokedollars ?>;
</script>

<script>
    // Optionele client-side check op balans vóór submit
    document.querySelectorAll('.bid-section').forEach(function(form) {
        const input = form.querySelector('.bidInput');
        const message = form.nextElementSibling; // .message

        form.addEventListener('submit', function (e) {
            if (!input || input.disabled) return;

            const value = Number(input.value);
            if (!value) {
                e.preventDefault();
                message.textContent = "Put an valid price in";
                message.style.color = "red";
                return;
            }

            if (value > userBalance) {
                e.preventDefault();
                message.textContent = "Not enough Poke$!";
                message.style.color = "red";
                return;
            }
        });
    });

    // ⏳ TIMER LOGICA
    function formatTime(seconds) {
        if (seconds < 0) seconds = 0;
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);

        const hh = String(h).padStart(2, '0');
        const mm = String(m).padStart(2, '0');
        const ss = String(s).padStart(2, '0');
        return `${hh}:${mm}:${ss}`;
    }

    document.querySelectorAll('.timer').forEach(function(timerEl) {
        let remaining = Number(timerEl.dataset.remaining);
        const timeText = timerEl.querySelector('.time-text');

        const card = timerEl.closest('.auction-card');
        const form = card.nextElementSibling; // .bid-section
        const input = form.querySelector('.bidInput');
        const button = form.querySelector('.bidButton');

        function closeAuction() {
            timerEl.textContent = "Auction ended";
            if (input) input.disabled = true;
            if (button) {
                button.disabled = true;
                button.textContent = "Closed";
            }
        }

        function tick() {
            if (remaining <= 0) {
                closeAuction();
                return;
            }

            timeText.textContent = formatTime(remaining);
            remaining--;

            setTimeout(tick, 1000);
        }

        if (remaining <= 0) {
            closeAuction();
        } else {
            tick();
        }
    });
</script>

</body>

