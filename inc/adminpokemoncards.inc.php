

<!-- Pokémon API Cards -->
<section id="pokemons" class="pokemons">
    <div class="container">
        <h2>Pokédex (API)</h2>
        <div class="pokecards-grid" id="apiGrid">
            <?php
            $url = "https://pokeapi.co/api/v2/pokemon?limit=20";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            foreach ($data['results'] as $index => $pokemon) {
                $pokeDetails = json_decode(file_get_contents($pokemon['url']), true);
                $name = ucfirst($pokeDetails['name']);
                $img = $pokeDetails['sprites']['front_default'];
                $type = ucfirst($pokeDetails['types'][0]['type']['name']);
                $price = rand(50, 500); // random prijs
                ?>
                <div class="pokecards-content" style="display: <?= $index < 6 ? 'block' : 'none' ?>;">
                    <img class="pokecards-img" src="<?= $img ?>" alt="<?= $name ?>">
                    <div class="pokecards-info">
                        <p class="pokecard-name"><?= $name ?></p>
                        <p class="pokecard-price">€<?= $price ?></p>
                        <p class="pokecard-desc">Type: <?= $type ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="load-more-wrapper">
            <button id="loadMoreApiBtn">Meer laden</button>
        </div>
    </div>
</section>

</div>


<!-- Script voor meer laden -->
<script>
    let visibleCount = 6;
    const cards = document.querySelectorAll("#apiGrid .pokecards-content");
    const loadMoreBtn = document.getElementById("loadMoreApiBtn");

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
</script>