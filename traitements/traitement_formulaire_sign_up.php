<?php
require "language.php";
?>
<?php
// Retrieve form data
$_SESSION['test_pwd'] = 5;
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$adresse = $_POST['rue'] . ", " . $_POST['code'] . " " . mb_strtoupper($_POST['ville']);
$pwd = $_POST['pwd'];
$Mail_Uti = $_POST['mail'];

$_SESSION['Mail_Temp'] = $Mail_Uti;

// Database connection
$utilisateur = "inf2pj02";
$serveur = "localhost";
$motdepasse = "ahV4saerae";
$basededonnees = "inf2pj_02";
$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);

// Retrieve the maximum Id_Uti value
$requete = "SELECT MAX(Id_Uti) AS id_max FROM UTILISATEUR";
$resultat = $connexion->query($requete);
$id_max = $resultat->fetch_assoc()['id_max'];

// Increment the Id_Uti value
$iduti = $id_max + 1;

// Check if the email address already exists
$requete2 = "SELECT COUNT(*) AS nb FROM UTILISATEUR WHERE Mail_Uti = '$Mail_Uti'";
$resultat2 = $connexion->query($requete2);
$nb = $resultat2->fetch_assoc()['nb'];

// Execute the insertion query if the email address is not already used
if ($nb == 0) {
    // Connect to the database with PDO
    $connexion = new PDO("mysql:host=$serveur;dbname=$basededonnees", $utilisateur, $motdepasse);

    // Set the error mode to Exception
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the insertion query for the user
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $insertionUtilisateur = $connexion->prepare("INSERT INTO UTILISATEUR (Id_Uti, Prenom_Uti, Nom_Uti, Adr_Uti, Pwd_Uti, Mail_Uti) VALUES (?, ?, ?, ?, ?, ?)");
    $insertionUtilisateur->execute([$iduti, $prenom, $nom, $adresse, $hashedPwd, $Mail_Uti]);

    echo $htmlEnregistrementUtilisateurReussi;

    // Create the producer if the profession is defined
    if (isset($_POST['profession'])) {
        $profession = $_POST['profession'];

        // Retrieve the last Id_Prod
        $requeteIdProd = $connexion->query("SELECT MAX(Id_Prod) AS id_max1 FROM PRODUCTEUR");
        $id_max_prod = $requeteIdProd->fetch(PDO::FETCH_ASSOC)['id_max1'];
        $id_max_prod++;

        // Prepare the insertion query for the producer
        $insertionProducteur = $connexion->prepare("INSERT INTO PRODUCTEUR (Id_Uti, Id_Prod, Prof_Prod) VALUES (?, ?, ?)");
        $insertionProducteur->execute([$iduti, $id_max_prod, $profession]);

        echo $htmlEnregistrementProducteurReussi;
    }

    // Check if the user is a producer
    $queryIsProd = $connexion->prepare('SELECT COUNT(*) as count FROM PRODUCTEUR WHERE Id_Uti = :id');
    $queryIsProd->execute(['id' => $iduti]);
    $returnIsProd = $queryIsProd->fetch(PDO::FETCH_ASSOC);
    $_SESSION["isProd"] = $returnIsProd['count'] > 0;

    $_SESSION['Mail_Uti'] = $Mail_Uti;
    $_SESSION['Id_Uti'] = $iduti;
    $_SESSION['erreur'] = '';
    if ($_SESSION["isProd"] == true) {
        $_POST['popup'] = 'addProfilPicture';
    } else {
        $_POST['popup'] = '';
    }
} else {
    $_SESSION['erreur'] = $htmlAdrMailDejaUtilisee;
}

// Close the connection
$connexion->close();
?>