

<?php
include '../Private/poketrader_db.connection.php';

$userId = $_SESSION['userId'] ?? null;
if (!$userId) {
    die("Je moet inloggen om je Pokémons te zien!");
}


$sql = "SELECT SUM(pokedollars) AS Pokedollars FROM wallet WHERE userId = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$wallet = $stmt->fetch();


$sqlCards = "SELECT * FROM pokecards WHERE userId = :userId";
$stmtCards = $pdo->prepare($sqlCards);
$stmtCards->execute(['userId' => $userId]);
$pokecards = $stmtCards->fetchAll();
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

<section id="pokemons" class="pokemons">
    <div class="container">
        <div class="pokecards-grid" id="userGrid">
            <?php if (!empty($pokecards)): ?>
                <?php foreach ($pokecards as $index => $card): ?>
                    <div class="pokecards-content" style="display: <?= $index < 6 ? 'block' : 'none' ?>;">
                        <img class="pokecards-img" src="<?= htmlspecialchars($card['img']) ?>" alt="<?= htmlspecialchars($card['name']) ?>">
                        <div class="pokecards-info">
                            <p class="pokecard-name"><?= htmlspecialchars($card['name']) ?></p>
                            <p class="pokecard-price"><?= htmlspecialchars($card['price']) ?> Pokedollars</p>
                            <p class="pokecard-desc">Type: <?= htmlspecialchars($card['type']) ?></p>
                            <p class="pokecard-status">Status: <?= htmlspecialchars($card['status']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>



            <?php else: ?>
                <p>Je hebt nog geen pokecards gekocht!</p>
            <?php endif; ?>
        </div>
        <?php if (count($pokecards) > 6): ?>
            <div class="loadMoreBtn">
                <button id="loadMoreBtn">Meer laden</button>
            </div>
        <?php endif; ?>
    </div>
</section>

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
