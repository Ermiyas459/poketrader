<?php
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'Klant'){
        include 'inc/navbarloggedin.inc.php';
    }
    else if ($_SESSION['role'] == 'Admin'){
        include 'inc/navbarADMIN.inc.php';
    }
}
else
{
    include 'inc/navbarNOTloggedin.inc.php';
}
?>