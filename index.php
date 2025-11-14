<?php
session_start();
if(isset($_GET['page']))
{
    $page = $_GET['page'];
}
else{
    $page = 'homepage2';
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

<?php if ($page != "pokedollarpage" && $page != "auction" && $page != "Auctionadmin" && $page != "inbox" && $page != "admininbox")
    { include 'inc/navbar.inc.php';} ?>

<?php include 'inc/' . $page . ".inc.php"; ?>

</body>
</html>

