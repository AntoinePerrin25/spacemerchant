<?php
session_start();
?>
<!DOCTYPE html>
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
        Shop
    </p>
</body>


<?php

    if(!isset($_SESSION['username'])) {
        echo "Session username is not defined. Please log in first.";
        header("Location: index.php");
    }
    else if(isset($_POST['method']) && $_POST['method'] == 'Logout') {
        echo "Logout";
        session_destroy();
        header("Location: index.php");
    }
    else if (isset($_POST['method']) && $_POST['method'] == 'Buy' && isset($_POST['modelID'])) {
        buySpaceship($_SESSION['UserID'], $_POST['modelID']);
    }
    
    echo "<h3 style='text-align: center'>Your money : ". getmoney(). "</h3>";
    echo "<p></p>";
    echo "<h2 style='text-align: center'>Your Spaceships</h2>";
    displayUserShips();
    echo "<p></p>";
    echo "<h2 style='text-align: center'>Available Spaceships</h2>";
    echo "<span id='space'>"
    . displayAvailableModels()."
    </span>";
    
?>
<p></p>
<span align="left">
    <form action="admin.php" method="post">
        <input style="width:150px; height:22px; text-align: center" type="submit" name="method" value="Logout from Session" />
    </form>
    <form action="mission.php" method="post">
        <input style="width:150px; height:22px; text-align: center" type="submit" value="Display Missions" />
    </form>
    <form action="index.php" method="post">
        <input style="width:150px; height:22px; text-align: center" type="submit" name="method" value="Logout">
    </form>
    <form action="admin.php" method="post">
        <input style="width:150px; height:22px; text-align: center" type="submit" name="method" value="Back to Admin">
    </form>
</span>
</html>

<?php
function displayUserShips()
{
    try {
        $userID = $_SESSION['UserID'];
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $req = $bdd->prepare('SELECT * FROM SpaceshipModel SM JOIN Spaceship S ON S.ModelID = SM.ModelID WHERE S.OwnerID = ?');
        $req->execute([$userID]);
        echo "<table>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Size</th>";
        echo "<th>Price</th>";
        echo "<th>FuelEfficiency</th>";
        echo "<th>FuelCapacity</th>";
        echo "<th>Speed</th>";
        echo "<th>Available</th>";
        echo "</tr>";

        while($data = $req->fetch()) {
            // request to check if the ship is available
            $shipID = $data['SpaceshipID'];
            $req2 = $bdd->prepare('SELECT Count(TransportedByShip) FROM Cargo WHERE TransportedByShip = ?');
            $req2->execute([$shipID]);
            $shipUsed = $req2->fetchColumn();
            echo "<tr>";
            echo "<td>" . $data['Name'] . "</td>";
            echo "<td>" . $data['Size'] . "</td>";
            echo "<td>" . $data['Price'] . "</td>";
            echo "<td>" . $data['FuelEfficiency'] . "</td>";
            echo "<td>" . $data['FuelCapacity'] . "</td>";
            echo "<td>" . $data['Speed'] . "</td>";
            if($shipUsed == 0)
            {
                echo "<td> &#10004 </td>";
            }
            else
            {
                echo "<td> &#10008 </td>";
            }
            echo "</tr>";

        }
        echo "</table>";
    
    } catch (Exception $e) {
        echo('Erreur : ' . $e->getMessage());
    }
}

function displayAvailableModels()
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $req = $bdd->prepare('SELECT * FROM SpaceshipModel');
        $req->execute();
        
        echo "<table>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Size</th>";
        echo "<th>Price</th>";
        echo "<th>FuelEfficiency</th>";
        echo "<th>FuelCapacity</th>";
        echo "<th>Speed</th>";
        echo "<th>Buy</th>";
        echo "</tr>";

        while($data = $req->fetch()) {
            echo "<tr>";
                echo "<td>" . $data['Name'] . "</td>";
                echo "<td>" . $data['Size'] . "</td>";
                echo "<td>" . $data['Price'] . "</td>";
                echo "<td>" . $data['FuelEfficiency'] . "</td>";
                echo "<td>" . $data['FuelCapacity'] . "</td>";
                echo "<td>" . $data['Speed'] . "</td>";
                echo "<td><form method='post' action='shop.php'>
                            <input type='hidden' name='modelID' value='" . $data['ModelID'] . "'>
                            <input type='submit' name='method' value='Buy'>
                        </form>
                    </td>";
            echo "</tr>";

        }
        echo "</table>";
    
    } catch (Exception $e) {
        echo('Erreur : ' . $e->getMessage());
    }

}

function getmoney()
{
    try {
        $userID = $_SESSION['UserID'];
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $req = $bdd->prepare('SELECT Money FROM user WHERE UserID = ?');
        $req->execute([$userID]);
        $money = $req->fetchColumn();
        return $money;
    } catch (Exception $e) {
        echo('Erreur : ' . $e->getMessage());
    }
}

function buySpaceship($userID, $modelID)
{
    try {
        $userID = $_SESSION['UserID'];


        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get user's money from the database
        
        $userMoney = getmoney();

        // Get spaceship model price
        $getSpaceshipPriceQuery = $bdd->prepare('SELECT Price FROM SpaceshipModel WHERE ModelID = ?');
        $getSpaceshipPriceQuery->execute([$modelID]);
        $spaceshipPrice = $getSpaceshipPriceQuery->fetchColumn();
        
        // Check if the user has enough money
        if ($userMoney >= $spaceshipPrice) {
            // Insert into 'Spaceship' table
            $insertSpaceshipQuery = $bdd->prepare('INSERT INTO Spaceship (Fuel, ModelID, OwnerID) VALUES (?, ?, ?)');
            $insertSpaceshipQuery->execute([0, $modelID, $userID]);

            // Deduct the money from the user's balance
            $updateUserMoneyQuery = $bdd->prepare('UPDATE user SET Money = Money - ? WHERE UserID = ?');
            $updateUserMoneyQuery->execute([$spaceshipPrice, $userID]);

            echo "Purchase successful!";
        } else {
            echo "Not enough money to buy this spaceship.";
        }

    } catch (Exception $e) {
        echo('Erreur : Try again later :' . $e->getMessage());
    }
}

?>
