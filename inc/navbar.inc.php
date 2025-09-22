<?php
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'Klant'){
        include 'inc/navbar-klant.inc.php';
    }
    else if ($_SESSION['role'] == 'Admin'){
        include 'inc/navbar-admin.inc.php';
    }
}
else
{
    include 'inc/navbar-Index.inc.php';
}
?>