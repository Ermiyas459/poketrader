<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Poketrader</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="customerdashboard">

<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION ['role'] == "Admin") {
    unset($_SESSION["role"]);
    header("Location: index.php?page=404");
    exit;
}

include '../Private/connectionpoke.php';
$userId = $_SESSION['userId'];

// kaarten
$sql = "SELECT card_id FROM collection WHERE user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute(['uid' => $userId]);
$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($rows)) {
    $in  = str_repeat('?,', count($rows) - 1) . '?';
    $sql = "SELECT * FROM pokecards WHERE id IN ($in)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($rows);
    $pokecards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $pokecards = [];
}

// pokedollars
$sql = "SELECT SUM(pokedollars) AS pokedollars 
        FROM wallet 
        WHERE userId = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute(['userId' => $userId]);
$wallet = $stmt->fetch();
?>

<section id="wallet" class="wallet">
    <div  class="wallet-container">
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
                <?php foreach ($pokecards as $index => $card): ?>
                    <div class="pokecards-content" <?= $index >= 6 ? 'style="display:none;"' : '' ?>>
                        <?php if (!empty($card['img'])): ?>
                            <img class="pokecards-img"
                                 src="<?= htmlspecialchars($card['img']) ?>"
                                 alt="<?= htmlspecialchars($card['name']) ?>">
                        <?php else: ?>
                            <div class="pokecards-img no-img">
                                No image
                            </div>
                        <?php endif; ?>

                        <div class="pokecards-info">
                            <p class="pokecard-name">
                                <?= htmlspecialchars($card['name']) ?>
                            </p>
                            <p class="pokecard-price">
                                €<?= htmlspecialchars($card['price']) ?>
                            </p>
                            <p class="pokecard-desc">
                                <?= htmlspecialchars($card['description']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($pokecards) > 6): ?>
                <div class="load-more-wrapper">
                    <button id="loadMoreBtn" class="btn-load-more">Load more</button>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p>No pokecards found in your collection.</p>
        <?php endif; ?>
    </div>
</section>

<a class="register-link" href="index.php?page=auction">Bids</a>

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

</body>
</html>
