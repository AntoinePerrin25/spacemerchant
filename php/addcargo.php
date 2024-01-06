<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "Session username is not defined. Please log in first.";
    header("Location: index.php");
    exit();
} else if (isset($_POST['method']) && $_POST['method'] == 'Logout') {
    echo "Logout";
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle cargo form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['method']) && $_POST['method'] == 'AddCargo') {
    $cargoName = $_POST['cargoName'];
    $cargoSize = $_POST['cargoSize'];

    // Database connection
    $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');

    // Insert cargo into the database
    $req = $bdd->prepare("INSERT INTO Cargo (CargoName, CargoSize) VALUES (?, ?)");
    $req->execute([$cargoName, $cargoSize]);

    echo "Cargo added successfully!";
    sleep(2);
    header("Location: addmission.php");
} else {

?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="utf-8" />
        <title>Space Merchant</title>
        <link rel="stylesheet" href="resources/css/style.css" />
        <link rel="stylesheet" href="resources/css/main title.css" />
    </head>

    <body>
        <p>
            Space Merchant <br />
            Add your Cargo
            <span align="left">
                <form action="admin.php">
                    <input type="submit" value="Back to Admin">
                </form>
                <form action="mission.php">
                    <input type="submit" value="Back to Missions">
                </form>
            </span>
        </p>

        <!-- Cargo Form -->
        <form action="addcargo.php" method="post">
            <label for="cargoName">Cargo Name:</label>
            <input type="text" name="cargoName" required>
            <br>

            <label for="cargoSize">Cargo Size:</label>
            <input type="number" name="cargoSize" required>
            <br>

            <input type="hidden" name="method" value="AddCargo">
            <input type="submit" value="Add Cargo">
        </form>

    </body>

    </html>
<?php
}
