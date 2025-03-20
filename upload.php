<?php
if(!isset($_SESSION)) {
    session_start();
}

use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["image"])) {
        $targetDir = __DIR__ . "/img_producteur/";
        
        $bdd = dbConnect();

        if (isset($_SESSION["Mail_Uti"])) {
            $mailUti = $_SESSION["Mail_Uti"];
        } else {
            $mailUti = $_SESSION["Mail_Temp"];
        }
        
        $requete = 'SELECT PRODUCTEUR.Id_Prod FROM PRODUCTEUR JOIN UTILISATEUR ON PRODUCTEUR.Id_Uti = UTILISATEUR.Id_Uti WHERE UTILISATEUR.Mail_Uti = :mail';
        $queryIdProd = $bdd->prepare($requete);
        $queryIdProd->bindParam(':mail', $mailUti, PDO::PARAM_STR);
        $queryIdProd->execute();
        $returnqueryIdProd = $queryIdProd->fetchAll(PDO::FETCH_ASSOC);
        $Id_Prod = $returnqueryIdProd[0]["Id_Prod"];

        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newFileName = $Id_Prod . '.' . $extension;
        $targetPath = $targetDir . $newFileName;
        
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            header('Location: ./index.php');
            exit;
        } else {
            $errorMessage = error_get_last()['message'] ?? 'Unknown error';
            header('Location: ./index.php?error=' . urlencode($errorMessage));
            exit;
        }
    } else {
        header('Location: ./index.php?error=no_image_selected');
        exit;
    }
}