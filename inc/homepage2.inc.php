<title>Homepage | Poketrader</title>
<body class="home-page">

      <div id="pokemonBackground">
          <div id="pokemonOverlay">
              Loading Pokémon...
          </div>

      </div>

      <div class="home-intro">
          <h2> Welcome to PokeTrader</h2>
          <p>

              Collect, trade, and play with unique PokeTrader! Here you'll find everything you need to expand your collection and trade with other Trainers.

          </p>
          <p>
              With<strong> PokéDollars</strong>, you can buy, sell, or earn special boosters. Become a true PokéMaster today and build your ultimate deck!
          </p>
      </div>

      <div class="card-carousel" id="cardCarousel"></div>

      <a href="index.php?page=login">
          <img src="images/pokelogo.png" alt="Logo" class="logoregister">
      </a>

      <script>
          document.addEventListener("DOMContentLoaded", () => {
              const apiBase = "https://pokeapi.co/api/v2";

              function typeColor(type) {
                  const colors = {
                      fire: "#F08030", water: "#6890F0", grass: "#78C850",
                      electric: "#F8D030", psychic: "#F85888", ice: "#98D8D8",
                      dragon: "#7038F8", dark: "#705848", fairy: "#EE99AC",
                      fighting: "#C03028", poison: "#A040A0", ground: "#E0C068",
                      flying: "#A890F0", bug: "#A8B820", rock: "#B8A038",
                      ghost: "#705898", steel: "#B8B8D0", normal: "#A8A878"
                  };
                  return colors[type] || "#AAA";
              }

              async function fetchPokemon(id) {
                  const res = await fetch(`${apiBase}/pokemon/${id}`);
                  return res.json();
              }

              async function createCard(id) {
                  const p = await fetchPokemon(id);

                  const card = document.createElement("div");
                  card.className = "pokemon-card";

                  const hpStat = p.stats.find(s => s.stat.name === "hp")?.base_stat || "??";
                  const move = p.moves[0]?.move.name || "???";

                  card.innerHTML = `
          <div class="header">#${p.id} ${p.name.toUpperCase()}</div>
          <div class="hp">HP ${hpStat}</div>
          <img src="${p.sprites.other["official-artwork"].front_default}" alt="${p.name}">
          <div class="pokemon-info">
              <div class="types"></div>
              <div class="move">Move: ${move}</div>
          </div>
        `;

                  // Add type badges
                  const typesDiv = card.querySelector(".types");
                  p.types.forEach(t => {
                      const span = document.createElement("span");
                      span.className = "type-badge";
                      span.textContent = t.type.name.toUpperCase();
                      span.style.background = typeColor(t.type.name);
                      typesDiv.appendChild(span);
                  });

                  return card;
              }

              function randomPokemonId() {
                  return Math.floor(Math.random() * 1010) + 1; // up to Gen 9
              }

              // Background follows the visible card
              function observeCards() {
                  const carousel = document.getElementById("cardCarousel");
                  const cards = carousel.querySelectorAll(".pokemon-card");

                  const observer = new IntersectionObserver(entries => {
                      entries.forEach(entry => {
                          if (entry.isIntersecting) {
                              const img = entry.target.querySelector("img");
                              const name = entry.target.querySelector(".header").textContent;
                              const hp = entry.target.querySelector(".hp").textContent;

                              document.getElementById("pokemonBackground").style.backgroundImage =
                                  `url(${img.src})`;

                              document.getElementById("pokemonOverlay").innerHTML = `
                        <h2 style="margin:0; font-size:24px;">${name}</h2>
                        <p style="margin:5px 0;">${hp}</p>
                    `;
                          }
                      });
                  }, { root: carousel, threshold: 0.6 });

                  cards.forEach(card => observer.observe(card));
              }

              async function loadCards() {
                  const carousel = document.getElementById("cardCarousel");
                  for (let i = 0; i < 100; i++) {
                      const id = randomPokemonId();
                      const card = await createCard(id);
                      carousel.appendChild(card);
                  }
                  observeCards();
              }

              loadCards();
          });
      </script>

</body>
