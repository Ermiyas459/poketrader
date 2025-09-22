<title>Homepage | Poketrader</title>

<body class="home-page">

<div class="slideshow-container">

    <div class="mySlides fade">
        <div class="numbertext">1 / 3</div>
        <img src="img/fan.jpg" style="width:100%">
        <div class="text">pokeCards</div>
    </div>

    <div class="mySlides fade">
        <div class="numbertext">2 / 3</div>
        <img src="img/page3.jpg" style="width:100%">
        <div class="text">traders</div>
    </div>

    <div class="mySlides fade">
        <div class="numbertext">3 / 3</div>
        <img src="img/1.jpg" style="width:100%">
        <div class="text">pokedollars</div>
    </div>

    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>

</div>
<br>

<div style="text-align:center">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
</div>

<!-- Intro + Cards -->
<div class="home-intro">
    <h2>Welkom bij PokéCards</h2>
    <p>
        Verzamel, ruil en speel met unieke PokéCards!
        Bij ons vind je alles wat je nodig hebt om je collectie uit te breiden en
        te ruilen met andere trainers.
    </p>
    <p>
        Met <strong>PokéDollars</strong> kun je kaarten kopen, verkopen of speciale boosters verkrijgen.
        Word vandaag nog een echte PokéMaster en bouw je ultieme deck!
    </p>
</div>

<div class="example-cards">
    <h3>Voorbeeldkaarten</h3>
    <div class="cards-grid">
        <div class="card">
            <img src="img/16.avif" alt="PokéCard 1">
            <p><strong>Pikachu</strong><br>⚡ Elektriciteit • 50 HP</p>
        </div>
        <div class="card">
            <img src="img/diancie.avif" alt="PokéCard 2">
            <p><strong>Charizard</strong><br>🔥 Vuur • 120 HP</p>
        </div>
        <div class="card">
            <img src="img/regirock.avif" alt="PokéCard 3">
            <p><strong>Bulbasaur</strong><br>🌱 Gras • 40 HP</p>
        </div>
    </div>
</div>



<script>
    let slideIndex = 1;
    showSlides(slideIndex);

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("mySlides");
        let dots = document.getElementsByClassName("dot");
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex-1].style.display = "block";
        dots[slideIndex-1].className += " active";
    }
</script>

</body>