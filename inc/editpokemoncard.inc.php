<title>Add Pokemoncard to Auction | ADMIN</title>

<?php
if (!isset($_SESSION['role']) || $_SESSION ['role'] == "Klant") {
unset($_SESSION["role"]);
header("Location: index.php?page=404");
exit;
}

include '../Private/connectionpoke.php'; // zelfde connectie als op auction

// Check of er een id is meegegeven
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: index.php?page=404");
    exit;
}

$cardId = (int)$_GET['id'];

// Kaart uit DB halen
$stmt = $pdo->prepare("SELECT * FROM pokecards WHERE id = :id");
$stmt->execute([':id' => $cardId]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    // Geen kaart gevonden
    header("Location: index.php?page=404");
    exit;
}
?>


<body class="addcard">
<div class="container">
    <h2>Edit Pokemoncard</h2>

    <div class="poke-card">
        <img id="preview" alt="Card Preview"
             src="<?= htmlspecialchars($card['img']) ?>">
        <p id="description">
            <?= htmlspecialchars($card['description']) ?>
        </p>
        <p id="priceDisplay">
            Price: €<?= number_format((float)$card['price'], 2, ',', '.') ?>
        </p>
    </div>

    <form action="php/editcard.php" method="post" enctype="multipart/form-data">
        <!-- Hidden: id van de kaart meesturen naar editcard.php -->
        <input type="hidden" name="id" value="<?= (int)$card['id'] ?>">

        <label for="name"></label>
        <input type="text"
               placeholder="Name Pokemon"
               id="name"
               name="name"
               required
               value="<?= htmlspecialchars($card['name']) ?>"
               oninput="updateCardInfo()">

        <label for="priceInput"></label>
        <input type="numberr"
               placeholder="Start-Price Pokemon (€)"
               id="priceInput"
               name="price"
               step="0.01"
               required
               value="<?= htmlspecialchars($card['price']) ?>"
               oninput="updateCardInfo()">

        <label for="descriptionInput"></label>
        <input type="text"
               placeholder="Describe the Pokemon card"
               id="descriptionInput"
               name="description"
               required
               value="<?= htmlspecialchars($card['description']) ?>">

        <label for="status"></label>
        <input type="numberr"
               placeholder="HP Pokemon"
               id="status"
               name="status"
               required
               value="<?= htmlspecialchars($card['status']) ?>"
               oninput="updateCardInfo()">

        <label for="img"></label>
        <input type="file"
               id="img"
               name="img"
               accept="image/*"
               onchange="previewImage(event)">
        <!-- je hoeft hier niet required, bij edit wil je vaak de oude afbeelding houden -->

        <label for="nature"></label>
        <select id="nature" name="nature" required onchange="updateCardInfo()">
            <option value="">Type Pokémon</option>
            <?php
            $types = [
                "Normal","Fire","Water","Electric","Grass","Ice",
                "Fighting","Poison","Ground","Flying","Psychic",
                "Bug","Rock","Ghost","Dragon","Dark","Steel","Fairy"
            ];
            foreach ($types as $type):
                ?>
                <option value="<?= $type ?>"
                    <?= $card['nature'] === $type ? 'selected' : '' ?>>
                    <?= $type ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div>
            <button type="submit" class="add-pokemon-btn">Edit Pokemon</button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('preview').src = reader.result;
        };
        reader.readAsDataURL(file);
    }

    function updateCardInfo() {
        const name = document.getElementById('name').value.trim() || "Unknown Pokémon";
        const hp = document.getElementById('status').value.trim() || "??";
        const priceVal = document.getElementById('priceInput').value;
        const type = document.getElementById('nature').value || "Unknown type";

        document.getElementById('description').textContent = `${name} is a ${type}-type with ${hp} HP.`;

        const priceNum = Number(priceVal);
        const priceText = isNaN(priceNum) ? '€0.00' : `€${priceNum.toFixed(2)}`;
        document.getElementById('priceDisplay').textContent = `Price: ${priceText}`;
    }

    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('preview').src = reader.result;
        };
        reader.readAsDataURL(file);
    }

    function updateCardInfo() {
        const name = document.getElementById('name').value.trim() || "Unknown Pokémon";
        const hp = document.getElementById('status').value.trim() || "??";
        const priceVal = document.getElementById('priceInput').value;
        const type = document.getElementById('nature').value || "Unknown type";

        document.getElementById('description').textContent = `${name} is a ${type}-type with ${hp} HP.`;

        const priceNum = Number(priceVal);
        const priceText = isNaN(priceNum) ? '€0.00' : `€${priceNum.toFixed(2)}`;
        document.getElementById('priceDisplay').textContent = `Price: ${priceText}`;
    }

    // Bij laden pagina meteen description/price updaten
    document.addEventListener('DOMContentLoaded', updateCardInfo);

</script>



<?php
// PHP: handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Use the same names as your form fields:
    $name   = htmlspecialchars($_POST["name"] ?? "");
    $price  = htmlspecialchars($_POST["price"] ?? "");
    $hp     = htmlspecialchars($_POST["status"] ?? "");
    $nature = htmlspecialchars($_POST["nature"] ?? "");

    echo "<script>alert('Card \"{$name}\" successfully added!');</script>";
}
?>
</body>
