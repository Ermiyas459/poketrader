<title>PokéDollar$ Shop | Poketrader</title>
<?php

include '../Private/connectionpoke.php';

// Get logged-in user ID (if any)
$userId = $_SESSION['userId'];

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
?>

<body class="pokedollarpage">
<main>
    <h2>Get Your Poké$!</h2>
    <p>Choose your pack and power up your Pokémon adventure.</p>


    <div class="cards">
        <div class="card" onclick="buy(5,50, 'Pikachu Pack')">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/25.png" alt="Pikachu">
            <h3>Pikachu Pack</h3>
            <div class="coins">50 Poké$</div>
            <div class="price">€5.00</div>
        </div>

        <div class="card" onclick="buy(10,120, 'Charmander Pack')">
            <div class="ribbon">+10%</div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/4.png" alt="Charmander">
            <h3>Charmander Pack</h3>
            <div class="coins">120 Poké$</div>
            <div class="price">€10.00</div>
        </div>

        <div class="card" onclick="buy(25,350, 'Bulbasaur Pack')">
            <div class="ribbon">+15%</div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/1.png" alt="Bulbasaur">
            <h3>Bulbasaur Pack</h3>
            <div class="coins">350 Poké$</div>
            <div class="price">€25.00</div>
        </div>

        <div class="card" onclick="buy(50,800, 'Squirtle Pack')">
            <div class="ribbon">+25%</div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/7.png" alt="Squirtle">
            <h3>Squirtle Pack</h3>
            <div class="coins">800 Poké$</div>
            <div class="price">€50.00</div>
        </div>

        <div class="card" onclick="buy(100,1800, 'Mewtwo Pack')">
            <div class="ribbon">+35%</div>
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/150.png" alt="Mewtwo">
            <h3>Mewtwo Pack</h3>
            <div class="coins">1800 Poké$</div>
            <div class="price">€100.00</div>
        </div>
    </div>
</main>

    <footer>
        <div class="balance">
            <a href="javascript:history.back()" class="ghost">Go back</a>

            <p class="pokedollars">
                <img src="images/pokedollar.png" class="currency-logo" alt="$">
                <span id="balance"><?= htmlspecialchars($pokedollars) ?></span>
                <br>
                <small>current Poké$ balance</small><br>
                <small><?= $userName ?></small>
            </p>
        </div>
    </footer>


    <script>
        function buy(price, coins, name) {
        <?php if (!$userId): ?>
        alert("You must be logged in to buy Poké$.");
        return;
        <?php endif; ?>

        const bevestiging = confirm(`Are you sure you want to buy ${coins} Poké$ for €${price}?`);

        if (bevestiging) {
        // Fill hidden form and submit
        document.getElementById('coins').value = coins;
        document.getElementById('price').value = price;
        document.getElementById('packName').value = name;

        document.getElementById('buyForm').submit();
    } else {
        alert("Payment Canceled");
    }
    }
</script>


<form id="buyForm" method="post" action="php/pokedollarpage.php">
    <input type="hidden" name="userId" value="<?= (int)$userId ?>">
    <input type="hidden" name="coins" id="coins">
    <input type="hidden" name="price" id="price">
    <input type="hidden" name="packName" id="packName">
</form>


</body>
