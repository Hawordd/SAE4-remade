<?php

if(!isset($_SESSION)){
    session_start();
}

// Database connection
$utilisateur = "inf2pj02";
$serveur = "localhost";
$motdepasse = "ahV4saerae";
$basededonnees = "inf2pj_02";
// Connect to database
$bdd = new PDO('mysql:host=' . $serveur . ';dbname=' . $basededonnees, $utilisateur, $motdepasse);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION["Id_Uti"]) && isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

    $stmt = $bdd->prepare('CALL broadcast_Producteur(:id_uti, :message)');
    $stmt->bindParam(':id_uti', $_SESSION["Id_Uti"], PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();

    header("Location: ../messagerie.php");
} else {
    echo "error";
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    var_dump(isset($_SESSION["Id_Uti"]));
    var_dump(isset($message));
}

?>