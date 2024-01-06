<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "Session username is not defined. Please log in first.";
    header("Location: index.php");
} else if (isset($_POST['method']) && $_POST['method'] == 'Logout from Session') {
    echo "Logout";
    session_destroy();
    header("Location: index.php");
}

else {
    if (isset($_GET['Message']))
    {
        echo $_GET['Message'];
    }
    echo '<!DOCTYPE html>
    <html lang="fr">
    <!-- Path: ../admin.php -->
    
    <head>
        <meta charset="utf-8" />
        <title>Space Merchant</title>
        <link rel="stylesheet" href="resources/css/main title.css" />
        <link rel="stylesheet" href="resources/css/style.css" />

    </head>
    
    <body>
        <p>
            Space Merchant <br />
            Admin Control Panel
        </p>
    </body>';
    echo "Welcome " . $_SESSION['username'] . " !";
    echo "<br/>";
    echo "<br/>";
}
?>
<span>
    <form action="admin.php" method="post">
        <input style="width:150px; height:22px; text-align: left" type="submit" name="method" value="Logout from Session" />
    </form>
    <form action="mission.php" method="post">
        <input style="width:150px; height:22px; text-align: left" type="submit" value="Display Missions" />
    </form>
    <form action="addmission.php" method="post">
        <input style="width:150px; height:22px; text-align: left" type="submit" name="method" value="Add a Mission" />
    </form>
    <form action="shop.php" method="post">
        <input style="width:150px; height:22px; text-align: left" type="submit" value="Go to Shop" />
    </form>
    <form action="creweditor.php" method="post">
        <input style="width:150px; height:22px; text-align: left" type="submit" value="Go to Crew Editor" />
    </form>

</span>