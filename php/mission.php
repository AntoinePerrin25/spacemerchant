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
        Missions
    </p>
</body>


<?php

if (!isset($_SESSION['username'])) {
    echo "Session username is not defined. Please log in first.";
    header("Location: index.php");
} else if (isset($_POST['method']) && $_POST['method'] == 'Logout') {
    echo "Logout";
    session_destroy();
    header("Location: index.php");
}
else if (isset($_GET['Message'])){
    echo "<strong>". $_GET['Message'] . "</strong>";
}

if (isset($_POST['mission'])) {
    switch ($_POST['mission']) {
        case 'Accept':
            if (isset($_POST['method2']) && $_POST['method2'] == 'accept2') {
                $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
                $checkAcceptedQuery = $bdd->prepare("SELECT AcceptedBy, PostedBy FROM missions WHERE MissionID = ?");
                $checkAcceptedQuery->execute([$missionid]);
                $result = $checkAcceptedQuery->fetch(PDO::FETCH_ASSOC);
                if ($result['AcceptedBy'] !== null) {
                    echo "Mission already accepted.";
                    return;
                } else if ($result['PostedBy'] == $userID) {
                    echo "You cannot accept your own mission.";
                    return;
                } else {
                    // Mettre à jour la mission avec le crew et le ship sélectionnés
                    $updateMissionQuery = $bdd->prepare("UPDATE missions SET AcceptedBy = ?, TransportedByCrew = ?, TransportedByShip = ? WHERE MissionID = ?");
                    $updateMissionQuery->execute([$userID, $crewID, $shipID, $missionid]);

                    if ($updateMissionQuery->rowCount() > 0) {
                        echo "Mission accepted successfully.";
                    } else {
                        echo "Failed to accept the mission.";
                    }
                    // Mettre à jour le cargo avec le crew et le ship sélectionnés
                    $updateCargoQuery = $bdd->prepare("UPDATE Cargo SET TransportedByShip = ?, TransportedByCrew = ? WHERE CargoID = ?");
                    $updateCargoQuery->execute([$shipID, $crewID, $cargoID]);
                }
            } else {
                acceptMission();
            }
            break;

        case 'Remove Mission':
            $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
            $cargoQuery = $bdd->prepare('SELECT CargoID FROM missions WHERE MissionID = ?');
            $cargoQuery->execute([$missionID]);
            $cargoID = $cargoQuery->fetch()['CargoID'];
            $deleteQuery = $bdd->prepare('DELETE FROM missions WHERE MissionID = ?');
            $deleteQuery->execute([$_POST['MissionID']]);
            $resetCargoQuery = $bdd->prepare('UPDATE Cargo SET TransportedByShip = NULL, TransportedByCrew = NULL WHERE CargoID = ?');
            $resetCargoQuery->execute([$cargoID]);
            break;
            
        case 'Abort':

            $missionID = $_POST['MissionID'];
            $userID = $_SESSION['UserID'];
            try
            {
                $bdd= new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
                // getting cargoID
                $reqCargo = $bdd->prepare("SELECT CargoID FROM missions WHERE MissionID = $missionID");
                $reqCargo->execute();
                $cargoID = $reqCargo->fetchColumn();

                $reqUpdateMission  =$bdd->prepare("UPDATE missions SET AcceptedBy = NULL WHERE MissionID = $missionID");
                $reqUpdateMission->execute();
            
                $reqUpdateCargo = $bdd->prepare("UPDATE Cargo SET TransportedByShip = NULL, TransportedByCrew = NULL WHERE CargoID = $cargoID");
                $reqUpdateCargo->execute();
                header("Location: mission.php?Message=Mission Aborted");
            }
            catch (PDOException $e)
            {
                echo "Connection failed: " . $e->getMessage();
                return;
            }
            break;

        default:
            echo "Unknown action";
            break;
    }
} else {
    displayAvailableMissions();
    displayUserMissions();
}


function displayAvailableMissions()
{

    $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
    $req = $bdd->prepare('SELECT m.MissionID, m.MissionName, m.Reward, c.CargoName, c.CargoSize, p1.PlanetName AS FromPlanet, p1.PlanetID AS P1ID, p2.PlanetName AS ToPlanet, p2.PlanetID AS P2ID 
                     FROM missions m 
                     JOIN Planet p1 ON m.FromPlanetID = p1.PlanetID
                     JOIN Planet p2 ON m.ToPlanetID = p2.PlanetID
                     JOIN Cargo c ON m.CargoID = c.CargoID
                     WHERE m.AcceptedBy IS NULL AND m.PostedBy != ?');
    $req->execute([$_SESSION['UserID']]);

    echo "<h2 style='text-align: center';>Available Missions</h2>";
    echo
    "<table>
        <tr>
            <th>Name</th>
            <th>From Planet</th>
            <th>To Planet</th>
            <th>Distance</th>
            <th>Reward</th>
            <th>Cargo Name</th>
            <th>Cargo Size</th>
            <th>Action</th>
        </tr>";
    while ($mission = $req->fetch()) {
        echo
        "<tr>
            <td>" . $mission['MissionName'] . "</td>
            <td>" . $mission['FromPlanet'] . "</td>
            <td>" . $mission['ToPlanet'] . "</td>
            <td>" . "Distance :" . planet_distances($mission['P1ID'], $mission['P2ID']) . "</td>
            <td>" . $mission['Reward'] . "</td>
            <td>" . $mission['CargoName'] . "</td>
            <td>" . $mission['CargoSize'] . "</td>
            <td>
                <form action='mission.php' method='post'>
                    <input type='submit' name='mission' value='Accept' />
                    <input type='hidden' name='MissionID' value='" . $mission['MissionID'] . "' />
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
}

function displayUserMissions()
{
    $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
    $userID = $_SESSION['UserID'];

    // Missions créées par l'utilisateur
    $createdMissionsQuery = $bdd->prepare('SELECT m.MissionID, m.MissionName, m.Reward, c.CargoName, c.CargoSize, p1.PlanetName AS FromPlanet, p1.PlanetID AS P1ID, p2.PlanetName AS ToPlanet, p2.PlanetID AS P2ID 
                                        FROM missions m 
                                        JOIN Planet p1 ON m.FromPlanetID = p1.PlanetID
                                        JOIN Planet p2 ON m.ToPlanetID = p2.PlanetID
                                        JOIN Cargo c ON m.CargoID = c.CargoID WHERE PostedBy = ?');
    $createdMissionsQuery->execute([$userID]);

    // Missions acceptées par l'utilisateur
    $acceptedMissionsQuery = $bdd->prepare('SELECT m.MissionID, m.MissionName, m.Reward, c.CargoName, c.CargoSize, p1.PlanetName AS FromPlanet, p1.PlanetID AS P1ID, p2.PlanetName AS ToPlanet, p2.PlanetID AS P2ID 
                                        FROM missions m 
                                        JOIN Planet p1 ON m.FromPlanetID = p1.PlanetID
                                        JOIN Planet p2 ON m.ToPlanetID = p2.PlanetID
                                        JOIN Cargo c ON m.CargoID = c.CargoID WHERE AcceptedBy = ?');
    $acceptedMissionsQuery->execute([$userID]);

    echo '<p>';
    echo '<h2 style="text-align: center";>Missions Created by You</h2>';
    displayMissionsTable($createdMissionsQuery, true);
    echo '</span>';
    echo '</p>';
    echo '<span id="acceptedMissions">';
    echo '<h2 style="text-align: center";>Missions Accepted by You</h2>';
    displayMissionsTable($acceptedMissionsQuery, false);
    echo '</p>';
}

function acceptMission()
{
    echo "<form action = 'acceptmission.php' method='post'>";
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $missionid = $_POST['MissionID'];
        $userID = $_SESSION['UserID'];
        echo
        //MissionID : ". $missionid ." UserID : " . $userID . "  <br>*/ . "
        "<label name='crew'>Crew :
                <select name='crew' required>";
        $req = $bdd->prepare("SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ? AND CrewID NOT IN (SELECT TransportedByCrew FROM Cargo WHERE TransportedByCrew IS NOT NULL);");
        $req->execute([$userID]);
        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $row['CrewID'] . "'>" . $row['CrewName'] . "</option>";
        }
        echo
        "</select>
            </label>";
    } catch (Exception $e) {
        echo "No crew available, please go to the crew editor";
    }
    echo "<br>";
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        // getting cargo size
        $req = $bdd->prepare("SELECT CargoSize FROM Cargo C JOIN missions M ON C.CargoID = M.CargoID WHERE M.MissionID = ?;");
        $req->execute([$missionid]);
        $cargoSize = $req->fetch(PDO::FETCH_ASSOC)['CargoSize'];


        $req = $bdd->prepare("  SELECT S.SpaceshipID, SM.Name
                                FROM Spaceship S
                                JOIN SpaceshipModel SM ON S.ModelID = SM.ModelID
                                WHERE OwnerID = ? 
                                AND S.SpaceshipID NOT IN (SELECT TransportedByShip FROM Cargo WHERE TransportedByShip IS NOT NULL)
                                AND SM.Size >= ?
                            ");
        $req->execute([$userID, $cargoSize]);
        echo
        "<br>
            <label name='ship'>Ship :</label> &nbsp
                <select name='ship'>";
        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $row['SpaceshipID'] . "'>" . $row['Name'] . "</option>";
        }
        echo "</select>";
    } catch (Exception $e) {
        echo "No ship available, please go to the shop";
    }
    echo "
        <input type='hidden' name='MissionID' value='$missionid' />
        <input type='hidden' name='method2' value='accept2' />
        <br>
        <input style='width:73px; height:22px; text-align: center' type='submit' name='method' value='Accept' />
        <input style='width:73px; height:22px; text-align: center' type='submit' name='method' value='Cancel' />
        </form>
        <form action='creweditor.php' method='post'>
            <input style='width:150px; height:22px; text-align: center' type='submit' value='Crew Editor' />
        </form>
        <form action='shop.php' method='post'>
            <input style='width:150px; height:22px; text-align: center' type='submit' value='Go to Shop'/>
        </form>";
}

function planet_distances($PlanetID1, $PlanetID2)
{
    $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
    $req = $bdd->prepare("SELECT CoordX, CoordY, CoordZ FROM Planet WHERE (PlanetID = ?)");
    $req->execute([$PlanetID1]);
    $planet1 = $req->fetch();
    $req = $bdd->prepare("SELECT CoordX, CoordY, CoordZ FROM Planet WHERE (PlanetID = ?)");
    $req->execute([$PlanetID2]);
    $planet2 = $req->fetch();
    $distance = round(sqrt(pow($planet1['CoordX'] - $planet2['CoordX'], 2) + pow($planet1['CoordY'] - $planet2['CoordY'], 2) + pow($planet1['CoordZ'] - $planet2['CoordZ'], 2)), 1);
    return $distance;
}

function displayMissionsTable($query, $removeMission)
{
    // test if query is empty
    if ($query->rowCount() == 0) {
        echo '<h3>No missions to display</h3>';
        return;
    }
    echo '<table>';
    echo '<tr>
            <th>Name</th>
            <th>From Planet</th>
            <th>To Planet</th>
            <th>Distance</th>
            <th>Reward</th>
            <th>Cargo Name</th>
            <th>Cargo Size</th>
            <th>Action</th>
          </tr>';

    while ($mission = $query->fetch()) {
        echo '<tr>
                <td>' . $mission['MissionName'] . '</td>
                <td>' . $mission['FromPlanet'] . '</td>
                <td>' . $mission['ToPlanet'] . '</td>
                <td>' . "Distance :" . planet_distances($mission['P1ID'], $mission['P2ID']) . '</td>
                <td>' . $mission['Reward'] . '</td>
                <td>' . $mission['CargoName'] . '</td>
                <td>' . $mission['CargoSize'] . '</td>
                <td>
                    <form action="mission.php" method="post">';
                    if ($removeMission)
                    {
                        echo '
                                <input type="submit" name="mission" value="Remove Mission" />
                                <input type="hidden" name="MissionID" value="' . $mission['MissionID'] . '" />
                            ';
                    }
                    else
                    {
                        echo '
                        <input type="submit" name="mission" value="Abort" />
                        <input type="hidden" name="MissionID" value="' . $mission['MissionID'] . '" />
                        ';
                    }
        echo '
                    </form>
                </td>
              </tr>';
    }

    echo '</table>';
}


?>
<span align="right">
    <form action="admin.php" methode="post">
        <input style='width:150px; height:22px; text-align: center' type="submit" name="method" value="Back to Admin" />
    </form>
    <form action="admin.php" method="post">
        <input style='width:150px; height:22px; text-align: center' type="submit" name="method" value="Logout from Session" />
    </form>
    <form action="mission.php" method="post">
        <input style='width:150px; height:22px; text-align: center' type="submit" value="Display Missions" />
    </form>
    <form action="addmission.php" method="post">
        <input style='width:150px; height:22px; text-align: center' type="submit" name="method" value="Add a Mission" />
    </form>
</span>

</html>