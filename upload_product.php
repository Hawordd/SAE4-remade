<?php
if(!isset($_SESSION)) {
    session_start();
}

require "language.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["image"]) && isset($_SESSION["Id_Produit"])) {
        $targetDir = __DIR__ . "/img_produit/";
        $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newFileName = htmlspecialchars($_SESSION["Id_Produit"]) . '.' . $extension;
        $targetPath = $targetDir . $newFileName;
        
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            header('Location: produits.php');
            exit;
        } else {
            $errorMessage = error_get_last()['message'] ?? 'Unknown error';
            header('Location: mes_produits.php?erreur=' . urlencode($errorMessage));
            exit;
        }
    } else {
        header('Location: produits.php');
        exit;
    }
} else {
    header('Location: produits.php');
    exit;
}