<?php
session_start();
if(isset($_GET["page"])){
    $page = $_GET["page"];
} else {
    $page = "home";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>dit is de index pagina</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
<header class="home-header">


    <nav class="home-navbar">



         <?php /*
        if(isset($_SESSION["role"])){
            if ($_SESSION["role"] == "Klant"){
                include "includes/navbar.inc.php";
            }
            elseif ($_SESSION["role"] == "Admin"){
                include "includes/navbar_admin.inc.php";
            }

        }
        else{
            include "includes/navbar_guest.inc.php";
        }

        */?>

    </nav>


</header>




<?php
include "includes/" . $page . ".inc.php";
?>

</body>
</html>