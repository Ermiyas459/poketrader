<?php
session_start();
if(isset($_GET['page']))
{
    $page = $_GET['page'];
}
else{
    $page = 'homepage';
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="./images/pokeballblue.png">
</head>
<body>
<?php include 'inc/navbar.inc.php'; ?>
<?php include 'inc/' . $page . ".inc.php"; ?>
</body>
</html>

