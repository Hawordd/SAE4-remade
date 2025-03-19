<?php

// Error handling with try-catch block
try {
    // Retrieve form data
    $pwd = $_POST['pwd'];
    $Mail_Uti = $_POST['mail'];

    // Handle password attempts
    if (!isset($_SESSION['test_pwd'])) {
        $_SESSION['test_pwd'] = 5;
    }

    // Database connection
    $utilisateur = "inf2pj02";
    $serveur = "localhost";
    $motdepasse = "ahV4saerae";
    $basededonnees = "inf2pj_02";

    // Connect to database
    $bdd = new PDO('mysql:host=' . $serveur . ';dbname=' . $basededonnees, $utilisateur, $motdepasse);

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
            // Check if user is an admin
            $queryIdAdmin = $bdd->prepare('SELECT Id_Uti FROM ADMINISTRATEUR WHERE Id_Uti = :id');
            $queryIdAdmin->execute(['id' => $Id_Uti]);
            $returnQueryIdAdmin = $queryIdAdmin->fetch(PDO::FETCH_ASSOC);

            if ($returnQueryIdAdmin == false) {
                echo ("
                <title>".$htmlErreur403."</title>
                <h1>".$htmlErreur403."</h1>
                <p>".$htmlPasAcces."</p>
                ");
            } else {
                $_SESSION['Mail_Uti'] = $Mail_Uti;
                $_SESSION['Id_Uti'] = $Id_Uti;
                $_SESSION['erreur'] = '';
                header('Location: panel_admin.php');
            }
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