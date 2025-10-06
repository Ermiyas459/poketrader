<title>Dashboard | Poketrader</title>


<?php
if (!isset($_SESSION['role']) || $_SESSION ['role'] == "Admin") {
    unset($_SESSION["role"]);
    header("Location: index.php?page=404");
    exit;
}

include '../Private/connectionpoke.php';

$id = $_SESSION['userId'];

// kaarten
$sql = "SELECT * FROM `pokecards`";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pokecards = $stmt->fetchAll();
// pokedollars
$id = $_SESSION['userId'] ?? 0; // fallback to 0 if not set

$sql = "SELECT SUM(pokedollars) AS pokedollars 
        FROM wallet 
        WHERE userId = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$wallet = $stmt->fetch();

?>

<section id="wallet" class="wallet">
    <div class="wallet-container">
        <div class="wallet-info">
            <p class="pokedollars">
                <img src="images/pokedollar.png" class="currency-logo" alt="$">
                <?= htmlspecialchars($wallet['pokedollars'] ?? 0) ?>
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
                            <p class="pokecard-price">€<?= htmlspecialchars($ticket['price']) ?></p>
                            <p class="pokecard-desc"><?= htmlspecialchars($ticket['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($pokecards) > 6): ?>
                <div class="load-more-wrapper">
                    <button id="loadMoreBtn">Load more</button>
                </div>

            <?php endif; ?>
        <?php else: ?>
            <p>No pokecards found.</p>
        <?php endif; ?>
    </div>
</section>

<button class="bidsbtn" id="bidsbtn">Bids</button>


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