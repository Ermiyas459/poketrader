<title>Login | Poketrader</title>
<div class="hero">
    <video autoplay muted loop playsinline>
        <source src="images/pokemon.mp4" type="video/mp4">
    </video>

<div class="login">
    <form action="./php/login.php" method="post">
        <div class="login-group">
            <a href="index.php?page=homepage">
                <img src="images/pokelogo.png" alt="Logo" class="logologin">
            </a>
        </div>
        <?php
        if(isset($_SESSION["meldinglogin"]))
        {
            echo $_SESSION["meldinglogin"];
            unset($_SESSION["meldinglogin"]);
        }
        ?>
        <h4>Login</h4>
        <div class="login-group">
            <input type="email" placeholder="✉ Email Adress" name="email" id="email" required>
        </div>
        <div class="login-group">

            <input type="password" placeholder="🔒 Password" name="password" id="password" required>
        </div>
        <div class="register-group-button">
            <input type="submit" value="LOGIN NOW">
        </div>
    </form>

    <?php
    if(isset($_SESSION["meldinglogin"]))
    {
        echo $_SESSION["meldinglogin"];
        unset($_SESSION["meldinglogin"]);
    }
    ?>

    <div class="register-link">
        <h6>Don't have a account yet? <a href="index.php?page=registration">Register</a></h6>
    </div>
</div>

