<title>Add Pokemoncard to Auction | ADMIN</title>





<body class="addcard">
<div class="container">
    <h2>Add Pokemoncard to Auction</h2>

    <div class="poke-card">
        <img id="preview" alt="Card Preview">
        <p id="description">The description of the Pokémon card appears here</p>
        <p id="priceDisplay">Price: €0.00</p>
    </div>

    <form action="php/addcard.php" method="post" enctype="multipart/form-data">

        <label for="name"></label>
        <input type="text" placeholder="Name Pokemon" id="name" name="name" required oninput="updateCardInfo()">

        <label for="priceInput"></label>
        <input type="numberr" placeholder="Start-Price Pokemon (€)" id="priceInput" name="price" step="0.01" required oninput="updateCardInfo()">

        <label for="descriptionInput"></label>
        <input type="text" placeholder="Describe the Pokemon card" id="descriptionInput" name="description" required>

        <label for="status"></label>
        <input type="numberr" placeholder="HP Pokemon" id="status" name="status" required oninput="updateCardInfo()">

        <label for="img"></label>
        <input type="file" id="img" name="img" accept="image/*" onchange="previewImage(event)" required>

        <label for="nature"></label>
        <select id="nature" name="nature" required onchange="updateCardInfo()">
            <option value="">Type Pokémon</option>
            <option value="Normal">Normal</option>
            <option value="Fire">Fire</option>
            <option value="Water">Water</option>
            <option value="Electric">Electric</option>
            <option value="Grass">Grass</option>
            <option value="Ice">Ice</option>
            <option value="Fighting">Fighting</option>
            <option value="Poison">Poison</option>
            <option value="Ground">Ground</option>
            <option value="Flying">Flying</option>
            <option value="Psychic">Psychic</option>
            <option value="Bug">Bug</option>
            <option value="Rock">Rock</option>
            <option value="Ghost">Ghost</option>
            <option value="Dragon">Dragon</option>
            <option value="Dark">Dark</option>
            <option value="Steel">Steel</option>
            <option value="Fairy">Fairy</option>
        </select>

        <div>
            <button type="submit" class="add-pokemon-btn">Add Pokemon</button>
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

