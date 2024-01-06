<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "Session username is not defined. Please log in first.";
    header("Location: index.php");
    exit();
}

if (isset($_POST['addCrew'])) {
    $crewName = $_POST['crewName'];
    addCrew($_SESSION['UserID'], $crewName);
}

if (isset($_POST['deleteCrew'])) {
    $crewIDToDelete = $_POST['CrewID'];
    deleteCrew($crewIDToDelete);
}

if (isset($_POST['addMember'])) {
    $crewID = $_POST['CrewID'];
    $memberName = $_POST['memberName'];
    addCrewMember($crewID, $memberName);
}

if (isset($_POST['deleteMember'])) {
    $memberIDToDelete = $_POST['MemberID'];
    deleteCrewMember($memberIDToDelete);
}

if (isset($_POST['moveMember'])) {
    $memberIDToMove = $_POST['MemberID'];
    $newCrewID = $_POST['newCrewID'];
    moveCrewMember($memberIDToMove, $newCrewID);
}

function addCrew($ownerID, $crewName)
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'équipage avec le même nom existe déjà pour cet utilisateur
        $checkCrewQuery = $bdd->prepare('SELECT COUNT(*) FROM Crew WHERE CrewName = ? AND CrewOwnerID = ?');
        $checkCrewQuery->execute([$crewName, $ownerID]);
        $count = $checkCrewQuery->fetchColumn();

        if ($count > 0) {
            echo "Crew with the same name already exists for this user.";
            return;
        }

        // Ajouter l'équipage à la base de données
        $addCrewQuery = $bdd->prepare('INSERT INTO Crew (CrewName, CrewOwnerID) VALUES (?, ?)');
        $addCrewQuery->execute([$crewName, $ownerID]);

        echo "Crew added successfully.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function deleteCrew($crewID)
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'équipage est associé à une mission

        $checkMissionQuery = $bdd->prepare('SELECT Count(TransportedByCrew) FROM Cargo WHERE TransportedByCrew = ?');
        $checkMissionQuery->execute([$crewID]);
        $missionCount = $checkMissionQuery->fetchColumn();

        if ($missionCount > 0) {
            echo "Cannot delete crew associated with an active mission.";
        } else {
            // Supprimer les membres de l'équipage
            $deleteMembersQuery = $bdd->prepare('DELETE FROM CrewMember WHERE CrewID = ?');
            $deleteMembersQuery->execute([$crewID]);

            // Supprimer l'équipage
            $deleteCrewQuery = $bdd->prepare('DELETE FROM Crew WHERE CrewID = ?');
            $deleteCrewQuery->execute([$crewID]);

            echo "Crew and its members deleted successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function addCrewMember($crewID, $memberName)
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'équipage est associé à une mission
        $checkMissionQuery = $bdd->prepare('SELECT COUNT(*) FROM missions JOIN Cargo ON Cargo.CargoID = missions.CargoID WHERE AcceptedBy IS NULL AND TransportedByCrew = ?');
        $checkMissionQuery->execute([$crewID]);
        $missionCount = $checkMissionQuery->fetchColumn();

        if ($missionCount > 0) {
            echo "Cannot add crew member to crew associated with an active mission.";
        } else {
            // Ajouter un membre à l'équipage
            $addMemberQuery = $bdd->prepare('INSERT INTO CrewMember (Name, CrewID) VALUES (?, ?)');
            $addMemberQuery->execute([$memberName, $crewID]);

            echo "Crew member added successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function deleteCrewMember($memberID)
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'équipage du membre est associé à une mission
        // Get the crew ID of the member
        $getCrewIDQuery = $bdd->prepare('SELECT CrewID FROM CrewMember WHERE MemberID = ?');
        $getCrewIDQuery->execute([$memberID]);
        $crewID = $getCrewIDQuery->fetchColumn();

        $checkMissionQuery = $bdd->prepare('SELECT Count(TransportedByCrew) FROM Cargo WHERE TransportedByCrew = ?');
        $checkMissionQuery->execute([$crewID]);
        $missionCount = $checkMissionQuery->fetchColumn();

        if ($missionCount > 0) {
            echo "Cannot delete crew member from crew associated with an active mission.";
        } else {
            // Supprimer le membre de l'équipage
            $deleteMemberQuery = $bdd->prepare('DELETE FROM CrewMember WHERE MemberID = ?');
            $deleteMemberQuery->execute([$memberID]);

            echo "Crew member deleted successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


function moveCrewMember($memberID, $newCrewID)
{
    try {
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'équipage du membre est associé à une mission
        // Get the crew ID of the member
        $getCrewIDQuery = $bdd->prepare('SELECT CrewID FROM CrewMember WHERE MemberID = ?');
        $getCrewIDQuery->execute([$memberID]);
        $crewID = $getCrewIDQuery->fetchColumn();
        $checkMissionQuery = $bdd->prepare('SELECT Count(TransportedByCrew) FROM Cargo WHERE TransportedByCrew = ?');
        $checkMissionQuery->execute([$memberID]);
        $missionCount1 = $checkMissionQuery->fetchColumn();
        // Vérifier si le nouvel équipage est associé à une mission
        $checkMissionQuery = $bdd->prepare('SELECT Count(TransportedByCrew) FROM Cargo WHERE TransportedByCrew = ?');
        $checkMissionQuery->execute([$newCrewID]);
        $missionCount2 = $checkMissionQuery->fetchColumn();


        if ($missionCount1 > 0 || $missionCount2 > 0) {
            echo "Cannot move crew member from crew associated with an active mission.";
        } else {
            // Mettre à jour l'ID de l'équipage du membre
            $moveMemberQuery = $bdd->prepare('UPDATE CrewMember SET CrewID = ? WHERE MemberID = ?');
            $moveMemberQuery->execute([$newCrewID, $memberID]);

            echo "Crew member moved successfully.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <title>Crew Editor</title>
    <link rel="stylesheet" href="resources/css/style.css" />
    <link rel="stylesheet" href="resources/css/main title.css" />
</head>

<body>
    <p>
        Crew Editor
    </p>
    <h1 style="text-align: left;">Your Crews</h1>
    <?php
    try
    {

        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Sélectionner les équipages de l'utilisateur avec leur nom et ID
        $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
        $getCrewsQuery->execute([$_SESSION['UserID']]);

        // Afficher les équipages et leurs membres
        while ($crewInfo = $getCrewsQuery->fetch(PDO::FETCH_ASSOC))
        {
            $crewID = $crewInfo['CrewID'];
            $crewName = $crewInfo['CrewName'];

            echo "<h3>{$crewName}</h3>";

            // Sélectionner les membres de l'équipage actuel
            $getCrewMembersQuery = $bdd->prepare('SELECT MemberID, Name FROM CrewMember WHERE CrewID = ?');
            $getCrewMembersQuery->execute([$crewID]);

            // Afficher les membres de l'équipage actuel
            if ($getCrewMembersQuery->rowCount() > 0) {
                echo "<ul>";
                while ($crewMember = $getCrewMembersQuery->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li>{$crewMember['Name']}</li>";
                }
                echo "</ul>";
            } else {
                echo "<h5>No members in this crew.</h5>";
            }
        }
    } catch (PDOException $e) {
        echo "No Crews Found";
    }

    ?>
    <p></p>
    <h1 style="text-align: left;">Modify your Crews</h1>
    <form action="creweditor.php" method="post">
        <label for="crewName">Crew Name:</label>
        <input type="text" name="crewName" required />
        <input type="submit" name="addCrew" value="Add Crew" />
    </form>

    <form action="creweditor.php" method="post">
        <label for="deleteCrew">Select Crew to Delete:</label>
        <select name="CrewID">
            <?php
            try {
                $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
                $getCrewsQuery->execute([$_SESSION['UserID']]);

                while ($crew = $getCrewsQuery->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$crew['CrewID']}'>{$crew['CrewName']}</option>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <input type="submit" name="deleteCrew" value="Delete Crew" />
    </form>

    <form action="creweditor.php" method="post">
        <label for="addMember">Select Crew to Add Member:</label>
        <select name="CrewID">
            <?php
            try {
                $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
                $getCrewsQuery->execute([$_SESSION['UserID']]);

                while ($crew = $getCrewsQuery->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$crew['CrewID']}'>{$crew['CrewName']}</option>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <label for="memberName">Member Name:</label>
        <input type="text" name="memberName" required />
        <input type="submit" name="addMember" value="Add Member" />
    </form>
    <form action="creweditor.php" method="post">
        <label for="deleteMember">Select Member to Delete:</label>
        <select name="MemberID">
            <?php
            try {
                $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
                $getCrewsQuery->execute([$_SESSION['UserID']]);

                while ($crew = $getCrewsQuery->fetch(PDO::FETCH_ASSOC)) {
                    $crewID = $crew['CrewID'];
                    $crewName = $crew['CrewName'];

                    $getCrewMembersQuery = $bdd->prepare('SELECT MemberID, Name FROM CrewMember WHERE CrewID = ?');
                    $getCrewMembersQuery->execute([$crewID]);

                    while ($crewMember = $getCrewMembersQuery->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$crewMember['MemberID']}'>{$crewMember['Name']} ({$crewName})</option>";
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <input type="submit" name="deleteMember" value="Delete Member" />
    </form>
    <form action="creweditor.php" method="post">
        <label for="moveMember">Select Member to Move:</label>
        <select name="MemberID">
            <?php
            try {
                $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
                $getCrewsQuery->execute([$_SESSION['UserID']]);

                while ($crew = $getCrewsQuery->fetch(PDO::FETCH_ASSOC)) {
                    $crewID = $crew['CrewID'];
                    $crewName = $crew['CrewName'];

                    $getCrewMembersQuery = $bdd->prepare('SELECT MemberID, Name FROM CrewMember WHERE CrewID = ?');
                    $getCrewMembersQuery->execute([$crewID]);

                    while ($crewMember = $getCrewMembersQuery->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$crewMember['MemberID']}'>{$crewMember['Name']} ({$crewName})</option>";
                    }
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <label for="newCrew">Select New Crew:</label>
        <select name="newCrewID">
            <?php
            try {
                $getCrewsQuery = $bdd->prepare('SELECT CrewID, CrewName FROM Crew WHERE CrewOwnerID = ?');
                $getCrewsQuery->execute([$_SESSION['UserID']]);

                while ($crew = $getCrewsQuery->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$crew['CrewID']}'>{$crew['CrewName']}</option>";
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <input type="submit" name="moveMember" value="Move Member" />
    </form>


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
    </body>


</html>