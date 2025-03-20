<?php
use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

try {
    $bdd = dbConnect();
    

    if (!isset($_POST['categorie']) || !isset($_POST['idCommande'])) {
        throw new Exception("Missing required parameters");
    }
    
    $Id_Statut = filter_input(INPUT_POST, 'categorie', FILTER_VALIDATE_INT);
    $Id_Commande = filter_input(INPUT_POST, 'idCommande', FILTER_VALIDATE_INT);
    
    if ($Id_Statut === false || $Id_Commande === false) {
        throw new Exception("Invalid input parameters");
    }
    
    if ($Id_Statut === null) {

        header('Location: delivery.php');
        exit;
    }
    

    $updateCommande = "UPDATE COMMANDE SET Id_Statut = :Id_Statut WHERE Id_Commande = :Id_Commande";
    $bindUpdateCommande = $bdd->prepare($updateCommande);
    $bindUpdateCommande->bindParam(':Id_Statut', $Id_Statut, PDO::PARAM_INT); 
    $bindUpdateCommande->bindParam(':Id_Commande', $Id_Commande, PDO::PARAM_INT);
    $bindUpdateCommande->execute();
    

    if ($Id_Statut == 3) {

        $queryGetProduitCommande = $bdd->prepare('SELECT Id_Produit, Qte_Produit_Commande 
                                                FROM produits_commandes 
                                                WHERE Id_Commande = :Id_Commande');
        $queryGetProduitCommande->bindParam(":Id_Commande", $Id_Commande, PDO::PARAM_INT);
        $queryGetProduitCommande->execute();
        $products = $queryGetProduitCommande->fetchAll(PDO::FETCH_ASSOC);
        

        foreach ($products as $product) {
            $updateProduit = "UPDATE PRODUIT 
                            SET Qte_Produit = Qte_Produit + :Qte_Produit_Commande 
                            WHERE Id_Produit = :Id_Produit";
            $bindUpdateProduit = $bdd->prepare($updateProduit);
            $bindUpdateProduit->bindParam(':Qte_Produit_Commande', $product['Qte_Produit_Commande'], PDO::PARAM_INT); 
            $bindUpdateProduit->bindParam(':Id_Produit', $product['Id_Produit'], PDO::PARAM_INT);
            $bindUpdateProduit->execute();
        }
    }
    
    // Redirect back to delivery page
    header('Location: delivery.php');
    exit;
    
} catch (Exception $e) {
    header('Location: delivery.php?error=1');
    exit;
}
?>