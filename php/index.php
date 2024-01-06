<?php
    session_start();
    define('loginForm', "<form action='index.php' method='post'>
            <input type='text' name='username' placeholder='Username'>
            <input type='password' name='password' placeholder='Password'>
            <input type='hidden' name='method' value='Login'>
            <input type='submit' value='Login'>
            </form>", false);
    define('registerForm', "<form action='index.php' method='post'>
            <input type='text' name='username' placeholder='Username'>
            <input type='password' name='password' placeholder='Password'>
            <input type='hidden' name='method' value='Register'>
            <input type='submit' value='Register'>
            </form>", false);
    define("passwordRegex", '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\\\'":|,.<>\/?]).{8,}$/', false);

?>

<!DOCTYPE html>
<html lang="fr">
<!-- Path: ../index.php -->

<head>
    <meta charset="utf-8" />
    <title>Space Merchant</title>
    <link rel="stylesheet" href="resources/css/main title.css" />
    <link rel="stylesheet" href="resources/css/style.css" />

</head>

<body>
    <p>
        Space Merchant <br />
        The Game
    </p>

<?php
    if (isset($_SESSION['username']))
    {
        echo "Welcome again" . $_SESSION['username'] . "!";
        header('Location: admin.php');  
    }

    if (isset($_POST['method'])) {
        $method = $_POST['method'];
    } else {
        $method = '';
    }
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    } else {
        $username = '';
    }
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }

    if ($method == 'Login')
    {
        echo "<table>";
        echo "<tr>Login or : </tr>";
        echo "<tr><form action='index.php' method='post'>";
        echo "<input type='submit' name='method' value='Register'>";
        echo "</form></tr>";
        echo "</table>";


        if ($username == '' || $password == '') {
            echo loginForm;
        } else {
            login($username, $password);
        }
    }
    else if ($method == 'Register')
    {
        echo "<table>";
        echo "<tr>Register or : </tr>";
        echo "<tr><form action='index.php' method='post'>";
        echo "<input type='submit' name='method' value='Login'>";
        echo "</form></tr>";
        echo "</table>";

        if ($username == '' || $password == '') {
            echo registerForm;
        } else {
            register($username, $password);
        }
    }
    else
    {
        echo "<form action='index.php' method='post'>";
        echo "<input type='submit' name='method' value='Login'>";
        echo "&nbsp";
        echo "<input type='submit' name='method' value='Register'>";
        echo "</form>";
    }
    if (isset($_SESSION['username'])) {
        echo "Welcome " . $_SESSION['username'] . "!";
    }

    function login($username, $password)
    {
        try {
            $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
            //echo "Connected to the database successfully!";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            return;
        }
        
        $req = $bdd->prepare("SELECT password, UserID FROM user WHERE username = ?;");
        $req->execute([$username]);
        $user = $req->fetch();
        if ($user == null) {
            echo "Username or Password do not match.";
            echo loginForm;
            return;
        }
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['UserID'] = $user['UserID'];
            header('Location: admin.php');
        } else {
            echo "Username or Password do not match.";
            echo loginForm;
        }
    }

    function register($username, $password)
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $bdd = new PDO('mysql:host=nanopi.fr;dbname=spacemerchant;charset=utf8', 'root', 'WqiM5XZNKcxPVb');
        $req = $bdd->prepare("SELECT Count(*) FROM user WHERE username = ?;");
        $req->execute([$username]);
        $user = $req->fetch();
        if ($user['Count(*)'] > 0) {
            echo "Username already taken.";
            echo registerForm;
            
            return;
        } else if (!preg_match(passwordRegex, $password)){
            echo "Password must contain at least 8 characters, 1 number, 1 uppercase letter, 1 lowercase letter and 1 special character.";
            echo registerForm;
            
            return;
        } else {
            // Hashing the password using the bcrypt algorithm
            $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            // Inserting the username and hashed password into the database
            $req = $bdd->prepare("INSERT INTO user (username, password) VALUES (?, ?);");
            $req->execute([$username, $password]);
            echo "Registered successfully.
            Redirecting to admin pannel ...";
            // request the UserID from the database
            $req = $bdd->prepare("SELECT UserID FROM user WHERE username = ?;");
            $req->execute([$username]);
            $user = $req->fetch();
            $_SESSION['username'] = $username;
            $_SESSION['UserID'] = $user['UserID'];
            header('Location: admin.php');
        }
    }

?>

</body>

</html>