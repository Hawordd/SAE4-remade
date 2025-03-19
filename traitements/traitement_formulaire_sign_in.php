<?php
<<<<<<< HEAD
    require "language.php" ; 
    use DBConfig\Database;
?>
<?php
=======
require "language.php";
session_start();
>>>>>>> e95100b65685eabe79ab39f495f52fd92c23ff0e

try {
    // Retrieve form data
    $pwd = $_POST['pwd'];
    $Mail_Uti = $_POST['mail'];

    // Handle password attempts
    if (!isset($_SESSION['test_pwd'])) {
        $_SESSION['test_pwd'] = 5;
    }



    // Database connection function
    function dbConnect(): PDO {
        return Database::getConnection();
    }
    // Connect to database
<<<<<<< HEAD
    $bdd = dbConnect();
=======
    $bdd = new PDO('mysql:host=' . $serveur . ';dbname=' . $basededonnees, $utilisateur, $motdepasse);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
>>>>>>> e95100b65685eabe79ab39f495f52fd92c23ff0e

    // Check if user email exists
    $queryIdUti = $bdd->prepare('SELECT Id_Uti, Pwd_Uti FROM UTILISATEUR WHERE Mail_Uti = :mail');
    $queryIdUti->execute(['mail' => $Mail_Uti]);
    $returnQueryIdUti = $queryIdUti->fetch(PDO::FETCH_ASSOC);

    // Handle invalid email
    if ($returnQueryIdUti == false) {
        unset($Id_Uti);
        $_SESSION['erreur'] = $htmlAdresseMailInvalide;
    } else {
        // Extract user ID and hashed password
        $Id_Uti = $returnQueryIdUti["Id_Uti"];
        $hashedPassword = $returnQueryIdUti["Pwd_Uti"];

        // Verify password
        if (password_verify($pwd, $hashedPassword)) {
            echo $htmlMdpCorrespondRedirectionAccueil;
            $_SESSION['Mail_Uti'] = $Mail_Uti;
            $_SESSION['Id_Uti'] = $Id_Uti;

            // Check if user is a producer
            $queryIsProd = $bdd->prepare('SELECT COUNT(*) as count FROM PRODUCTEUR WHERE Id_Uti = :id');
            $queryIsProd->execute(['id' => $Id_Uti]);
            $returnIsProd = $queryIsProd->fetch(PDO::FETCH_ASSOC);
            $_SESSION["isProd"] = $returnIsProd['count'] > 0;

            // Check if user is an admin
            $queryIsAdmin = $bdd->prepare('SELECT COUNT(*) as count FROM ADMINISTRATEUR WHERE Id_Uti = :id');
            $queryIsAdmin->execute(['id' => $Id_Uti]);
            $returnIsAdmin = $queryIsAdmin->fetch(PDO::FETCH_ASSOC);
            $_SESSION["isAdmin"] = $returnIsAdmin['count'] > 0;

            $_SESSION['erreur'] = '';
        } else {
            $_SESSION['test_pwd']--;
            $_SESSION['erreur'] = $htmlMauvaisMdp . $_SESSION['test_pwd'] . $htmlTentatives;
        }
    }

    if ($_SESSION['test_pwd'] <= -10) {
        $_SESSION['erreur'] = $htmlErreurMaxReponsesAtteintes;
    }
} catch (Exception $e) {
    // Handle any exceptions
    echo "An error occurred: " . $e->getMessage();
}
?>