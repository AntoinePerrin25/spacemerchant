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
else
{
    $userID = $_SESSION['UserID'];
    $crew = $_POST['crew'];
    $ship = $_POST['ship'];
    $missionID = $_POST['MissionID'];
    
    try
    {
        $bdd= new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $req  =$bdd->prepare("UPDATE missions SET AcceptedBy = $userID WHERE MissionID = $missionID");
        $req->execute();
        // getting cargoID
        $req2 = $bdd->prepare("SELECT CargoID FROM missions WHERE MissionID = $missionID");
        $req2->execute();
        $cargoID = $req2->fetchColumn();
       
        $req3 = $bdd->prepare("UPDATE Cargo SET TransportedByShip = $ship , TransportedByCrew = $crew WHERE CargoID = $cargoID");
        $req3->execute();
        header("Location: mission.php?Message=Mission Accepted");

    }
    catch (PDOException $e)
    {
        echo "Connection failed: " . $e->getMessage();
        return;
    }
}



?>