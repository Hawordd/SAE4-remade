<?php
header('Content-Type: text/html; charset=utf-8');

use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

$bdd = dbConnect();
$Id_Commande = htmlspecialchars($_POST["idCommande"]);

$query = 'SELECT Desc_Statut, COMMANDE.Id_Prod, COMMANDE.Id_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Prenom_Uti, 
                 COMMANDE.Id_Statut, UTILISATEUR.Adr_Uti, UTILISATEUR.Mail_Uti 
          FROM COMMANDE 
          INNER JOIN info_producteur ON COMMANDE.Id_Prod=info_producteur.Id_Prod 
          INNER JOIN STATUT ON COMMANDE.Id_Statut=STATUT.Id_Statut 
          INNER JOIN UTILISATEUR ON COMMANDE.Id_Uti=UTILISATEUR.Id_Uti 
          WHERE COMMANDE.Id_Commande = :idCommande';
$queryGetCommande = $bdd->prepare($query);
$queryGetCommande->bindParam(':idCommande', $Id_Commande, PDO::PARAM_INT);
$queryGetCommande->execute();

$returnQueryGetCommande = $queryGetCommande->fetchAll(PDO::FETCH_ASSOC);

$Id_Prod = $returnQueryGetCommande[0]["Id_Prod"];
$Desc_Statut = $returnQueryGetCommande[0]["Desc_Statut"];
$Nom_Uti = mb_strtoupper($returnQueryGetCommande[0]["Nom_Uti"]);
$Prenom_Uti = $returnQueryGetCommande[0]["Prenom_Uti"];
$Id_Statut = $returnQueryGetCommande[0]["Id_Statut"];
$Mail_Uti = $returnQueryGetCommande[0]["Mail_Uti"];
$Adr_Uti = $returnQueryGetCommande[0]["Adr_Uti"];

$query = 'SELECT Prenom_Uti, Nom_Uti, Mail_Uti, Adr_Uti, Prof_Prod 
          FROM info_producteur 
          WHERE Id_Prod = :idProducteur';
$queryGetProducteur = $bdd->prepare($query);
$queryGetProducteur->bindParam(':idProducteur', $Id_Prod, PDO::PARAM_INT);
$queryGetProducteur->execute();
$returnQueryGetProducteur = $queryGetProducteur->fetchAll(PDO::FETCH_ASSOC);

$Nom_Prod = mb_strtoupper($returnQueryGetProducteur[0]["Nom_Uti"]);
$Prenom_Prod = $returnQueryGetProducteur[0]["Prenom_Uti"];
$Adr_Prod = $returnQueryGetProducteur[0]["Adr_Uti"];
$Mail_Prod = $returnQueryGetProducteur[0]["Mail_Uti"];
$Prof_Prod = $returnQueryGetProducteur[0]["Prof_Prod"];

require('tfpdf/tfpdf.php');

class MonPDF extends tFPDF
{
    private $pdf;

    public function setPDF($pdf)
    {
        $this->pdf = $pdf;
    }

    function Header()
    {
        $this->pdf->SetFont('Arial', 'B', 12, 'UTF-8');
        $this->pdf->Cell(0, 5, 'Bon de commande', 0, 1, 'C');
        $this->pdf->Cell(0, 0, '', 'T');
        $this->pdf->Ln(5);
    }

    function Footer()
    {
        $this->pdf->SetY(-15);
        $this->pdf->SetFont('Arial', 'I', 12, 'UTF-8');
        $this->pdf->Cell(0, 10, 'Page ' . $this->pdf->PageNo(), 0, 0, 'C');
    }
}

$pdf = new MonPDF();
$pdf->setPDF($pdf);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 12, 'UTF-8');

$pdf->Cell(0, 5, $Prenom_Prod.' '.$Nom_Prod, 0, 1);
$pdf->Cell(0, 5, $Prof_Prod, 0, 1);
$pdf->Cell(0, 5, $Mail_Prod, 0, 1);
$pdf->Cell(0, 5, $Adr_Prod, 0, 1);

$pdf->Cell(0, 5, $Prenom_Uti.' '.$Nom_Uti, 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(0, 5, $Mail_Uti, 0, 0, 'R');
$pdf->Ln();
$pdf->Cell(0, 5, $Adr_Uti, 0, 0, 'R');
$pdf->Ln(5);

$pdf->Cell(0, 5, 'COMMANDE '.$Id_Commande.' :', 0, 1);

$pdf->SetFont('Arial', 'B', 12, 'UTF-8');
$pdf->Cell(40, 8, 'PRODUIT', 1);
$pdf->Cell(40, 8, 'PRIX UNITAIRE', 1);
$pdf->Cell(30, 8, 'QUANTITE', 1);
$pdf->Cell(40, 8, 'PRIX', 1);
$pdf->Ln();

$total = 0;
$query = 'SELECT Nom_Produit, Qte_Produit_Commande, Prix_Produit_Unitaire, Nom_Unite_Prix 
          FROM produits_commandes  
          WHERE Id_Commande = :idCommande';

$queryGetProduitCommande = $bdd->prepare($query);
$queryGetProduitCommande->bindParam(':idCommande', $Id_Commande, PDO::PARAM_INT);
$queryGetProduitCommande->execute();
$returnQueryGetProduitCommande = $queryGetProduitCommande->fetchAll(PDO::FETCH_ASSOC);

$produits = [];

foreach ($returnQueryGetProduitCommande as $product) {
    $Nom_Produit = $product["Nom_Produit"];
    $Qte_Produit_Commande = $product["Qte_Produit_Commande"];
    $Nom_Unite_Prix = $product["Nom_Unite_Prix"];
    $Prix_Produit_Unitaire = $product["Prix_Produit_Unitaire"];
    array_push($produits, [$Nom_Produit, $Prix_Produit_Unitaire, $Qte_Produit_Commande.' '.$Nom_Unite_Prix]);
    $total += intval($Prix_Produit_Unitaire) * intval($Qte_Produit_Commande);
}

$pdf->SetFont('Arial', '', 12, 'UTF-8');
foreach ($produits as $produit) {
    $pdf->Cell(40, 8, $produit[0], 1);
    $pdf->Cell(40, 8, $produit[1].' euros', 1); 
    $pdf->Cell(30, 8, $produit[2], 1);
    $pdf->Cell(40, 8, intval($produit[1]) * intval($produit[2]).' euros', 1); 
    $pdf->Ln();
}

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12, 'UTF-8');
$pdf->Cell(110, 8, 'TOTAL', 1);
$pdf->Cell(40, 8, $total.' euros', 1);
$pdf->Ln();

$pdf->Ln(5);

date_default_timezone_set('Europe/Paris');
$date = new DateTime('now');
$pdf->Cell(0, 5, "Date d'impression : " . $date->format('Y-m-d H:i:s'), 0, 1);

$nom_fichier = tempnam(sys_get_temp_dir(), 'pdf');
$pdf->Output($nom_fichier, 'F', true, 'UTF-8');

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Commande_'.$Id_Commande.'.pdf"');
header('Content-Length: ' . filesize($nom_fichier));

readfile($nom_fichier);
unlink($nom_fichier);
?>