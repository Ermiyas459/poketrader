<title>Registration | Poketrader</title>

<body class="registration-page">

<div class="registrationformm">
    <div>
        <a href="index.php?page=homepage">
            <img src="images/pokelogo.png" alt="Logo" class="logoregister">
        </a>
    </div>
    <?php
    if(isset($_SESSION["meldingpsw"]))
    {
        echo $_SESSION["meldingpsw"];
        unset($_SESSION["meldingpsw"]);
    }
    ?>
    <h4> Registration</h4>
    <form action="./php/register.php" method="post">
        <div class="register-group">
            <input type="text" placeholder="Enter ur name" name="name" id="name" required>
        </div>
        <div class="register-group">
            <input type="email" placeholder="Enter ur email" name="email" id="email" required>
        </div>
        <div class="register-group">
<!--            <h5>Enter ur Birthdate</h5>-->
            <input type="date" name="birthdate" id="birthdate" required>
        </div>
        <div class="register-group">
            <input type="password" placeholder="Create password" name="password" id="password" required>
        </div>
        <div class="register-group">
            <input type="password" placeholder="Confirm password" name="confirmpassword" id="confirmpassword" required>
        </div>


        <div class="policy">
            <h6>I accept all  <a href="index.php?page=terms">terms & conditions</a> <input type="checkbox" required>
        </div>
        <div class="register-group-button">
            <input type="submit" value="REGISTER NOW">
        </div>
        <div class="textregister">
            <h6> Already have an account? <a href="index.php?page=login">Login now</a></h6>
        </div>

    </form>

</div>

</body>