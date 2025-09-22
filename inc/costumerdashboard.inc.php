<?php
include '../Private/poketrader_db.connection.php';
$userId = $_SESSION['userId'];

// kaarten
$sql = "SELECT * FROM `pokecards`";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pokecards = $stmt->fetchAll();
// pokedollars
$sql = "SELECT SUM(pokedollars) AS Pokedollars 
        FROM wallet 
        WHERE userId = $userId";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$wallet = $stmt->fetch();


?>

<section id="wallet" class="wallet">
    <div class="wallet-container">
        <div class="wallet-info">
            <p class="pokedollars">
                 <img src="img/pokedollar" class="currency-logo" alt="$">
                <?= htmlspecialchars($wallet['Pokedollars'] ?? 0) ?>
            </p>
        </div>
    </div>
</section>


<section id="pokecards" class="pokecards">
    <div class="container">
        <?php if (!empty($pokecards)): ?>
            <div class="pokecards-grid" id="pokecardsGrid">
                <?php foreach ($pokecards as $index => $ticket): ?>
                    <div class="pokecards-content" style="display: <?= $index < 6 ? 'block' : 'none' ?>;">
                        <img class="pokecards-img" src="data:image/jpeg;base64,<?= base64_encode($ticket['img']) ?>" alt="<?= htmlspecialchars($ticket['name']) ?>">
                        <div class="pokecards-info">
                            <p class="pokecard-name"><?= htmlspecialchars($ticket['name']) ?></p>
<!--                           <img src="img/pokedollar" class="currency" alt="$"> -->
                            <p class="pokecard-price"><?= htmlspecialchars($ticket['price']) ?></p>
                            <p class="pokecard-desc"><?= htmlspecialchars($ticket['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($pokecards) > 6): ?>
                <div class="load-more-wrapper">
                    <button id="loadMoreBtn">Meer laden</button>
                </div>

            <?php endif; ?>
        <?php else: ?>
            <p>Geen pokecards gevonden.</p>
        <?php endif; ?>
    </div>
</section>

<button class="bidsbtn" id="bidsbtn">bids</button>


<script>
    let visibleCount = 6;
    const cards = document.querySelectorAll(".pokecards-content");
    const loadMoreBtn = document.getElementById("loadMoreBtn");

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", () => {
            let nextCount = visibleCount + 6;
            for (let i = visibleCount; i < nextCount && i < cards.length; i++) {
                cards[i].style.display = "block";
            }
            visibleCount = nextCount;
            if (visibleCount >= cards.length) {
                loadMoreBtn.style.display = "none";
            }
        });
    }
</script>
