<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "Session username is not defined. Please log in first.";
    header("Location: index.php");
    exit();
}

if (isset($_POST['method']) && $_POST['method'] == 'AddMission') {
    // Test if all variables are set (at once)
    if (!isset($_POST['missionName'], $_POST['planetFrom'], $_POST['planetTo'], $_POST['cargoID'], $_POST['reward'])) {
    }
    $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');

    $missionName = $_POST['missionName'];
    $planetFrom = $_POST['planetFrom'];
    $planetTo = $_POST['planetTo'];
    $CargoID = $_POST['cargoID'];
    // sql request to collect cargosize
    $req = $bdd->prepare("SELECT CargoSize FROM Cargo WHERE CargoID = ?");
    $req->execute([$CargoID]);
    $cargoSize = $req->fetch(PDO::FETCH_ASSOC)['CargoSize'];

    $reward = $_POST['reward'];
    $userID = $_SESSION['UserID'];


    $req = $bdd->prepare("INSERT INTO missions (MissionName, FromPlanetID, ToPlanetID, CargoID, Reward, PostedBy, AcceptedBy) VALUES (?, ?, ?, ?, ?, ?, NULL)");
    $req->execute([$missionName, $planetFrom, $planetTo, $CargoID, $reward, $userID]);

    header("Location: admin.php");
} else {
    // Formulaire d'ajout de mission
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
            Space Merchant <br>
            Add a Mission
        </p>
        <table>
            <form action="addmission.php" method="post">
                <tr>
                    <td><label for="missionName">Mission Name:</label></td>
                    <td><input style="width:150px; height:22px; text-align: center" type="text" name="missionName" required></td>
                </tr>
                <tr>
                    <td><label for="planetFrom">Departure Planet:</label></td>
                    <td>
                        <select name="planetFrom" style="width:150px; height:22px; text-align: center" required>
                            <?php
                            // Récupérez la liste des planètes depuis la base de données
                            $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
                            $req = $bdd->prepare("SELECT PlanetID, PlanetName FROM Planet");
                            $req->execute();
                            while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['PlanetID'] . "'>" . $row['PlanetName'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="planetTo">Destination Planet :</label></td>
                    <td>
                        <select name="planetTo" style="width:150px; height:22px; text-align: center" required>
                            <?php
                            // Récupérez à nouveau la liste des planètes depuis la base de données
                            $req = $bdd->prepare("SELECT PlanetID, PlanetName FROM Planet");
                            $req->execute();
                            while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['PlanetID'] . "'>" . $row['PlanetName'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="cargoID">CargoID :</label></td>
                    <td>
                        <select name="cargoID" style="width:150px; height:22px; text-align: center" required>
                            <?php
                            // Récupérez la liste des cargo depuis la base de données
                            $req = $bdd->prepare("SELECT CargoID, CargoName, CargoSize FROM Cargo WHERE CargoID NOT IN (SELECT CargoID FROM missions WHERE CargoID IS NOT NULL);");
                            $req->execute();
                            while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['CargoID'] . "'>" . $row['CargoName'] . " : " . $row['CargoSize'] . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="reward">Reward :</label></td>
                    <td><input type="number" style="width:150px; height:22px; text-align: center" name="reward" required></td>
                </tr>
                <tr>
                    <td>Validate : </td>
                    <td>
                        <input type="hidden" name="method" value="AddMission">
                        <input type="submit" style="width:150px; height:22px; text-align: center" value="Add Mission">
                    </td>
                </tr>
        </table>
        </form>
        <p></p>
        <span align="center">
            <form action="admin.php" method="post">
                <input style="width:150px; height:22px; text-align: center" type="submit" name="method" value="Logout from Session" />
            </form>
            <form action="addcargo.php">
                <input style="width:150px; height:22px; text-align: center" type="submit" value="Add a Cargo Here">
            </form>
            <form action="mission.php" method="post">
                    <input style="width:150px; height:22px; text-align: center" type="submit" value="Display Missions" />
            </form>
            <form action="admin.php" method="post">
                <input style="width:150px; height:22px; text-align: center" type="submit" name="method" value="Back to Admin">
            </form>
        </span>
    <p></p>
    </body>
    </html>
<?php
}
?>