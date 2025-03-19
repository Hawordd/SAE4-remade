<?php
// Start session at the very beginning
if (!isset($_SESSION)) {
    session_start();
}

// Include language file
require "language.php";

// Database connection function
function dbConnect() {
    require_once __DIR__ . '/DBConfig/Database.php';
    require_once __DIR__ . '/DBConfig/Config.php';
    
    return \DBConfig\Database::getConnection();
}

// Process GET parameters
$rechercheVille = isset($_GET["rechercheVille"]) ? htmlspecialchars($_GET["rechercheVille"]) : "";
$_GET["categorie"] = isset($_GET["categorie"]) ? $_GET["categorie"] : "Tout";
$utilisateur = isset($_SESSION["Id_Uti"]) ? htmlspecialchars($_SESSION["Id_Uti"]) : -1;
$rayon = isset($_GET["rayon"]) ? htmlspecialchars($_GET["rayon"]) : 100;
$tri = isset($_GET["tri"]) ? htmlspecialchars($_GET["tri"]) : "nombreDeProduits";
$_SESSION["language"] = isset($_SESSION["language"]) ? $_SESSION["language"] : "fr";

// Define language variables
$htmlFrançais = "Français";
$htmlAnglais = "English";
$htmlEspagnol = "Español";
$htmlAllemand = "Deutch";
$htmlRusse = "русский";
$htmlChinois = "中國人";

// Function to get GPS coordinates
function latLongGps($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "LEtalEnLigne/1.0");
    curl_setopt($ch, CURLOPT_REFERER, "https://proxy.univ-lemans.fr:3128");
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        $data = json_decode($response);
        
        if (!empty($data) && is_array($data) && isset($data[0])) {
            $latitude = $data[0]->lat;
            $longitude = $data[0]->lon;
            return [$latitude, $longitude];
        }
        return [0, 0];
    }
    
    curl_close($ch);
}

// Distance calculation function
function distance($lat1, $lng1, $lat2, $lng2, $miles = false) {
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lng1 *= $pi80;
    $lat2 *= $pi80;
    $lng2 *= $pi80;

    $r = 6372.797; // mean radius of Earth in km
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $km = $r * $c;

    return ($miles ? ($km * 0.621371192) : $km);
}

// Get user address
$mabdd = dbConnect();           
$queryAdrUti = $mabdd->prepare('SELECT Adr_Uti FROM UTILISATEUR WHERE Id_Uti = :utilisateur');
$queryAdrUti->bindParam(":utilisateur", $utilisateur, PDO::PARAM_STR);
$queryAdrUti->execute();
$returnQueryAdrUti = $queryAdrUti->fetchAll(PDO::FETCH_ASSOC);

if (count($returnQueryAdrUti) > 0) {
    $Adr_Uti_En_Cours = $returnQueryAdrUti[0]["Adr_Uti"];
} else {
    $Adr_Uti_En_Cours = 'France';
}

// Handle popup session management
if (isset($_SESSION['tempPopup'])) {
    $_POST['popup'] = $_SESSION['tempPopup'];
    unset($_SESSION['tempPopup']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo $htmlMarque; ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body>
    <div class="container">
        <!-- Left column with search filters -->
        <div class="leftColumn">
            <img class="logo" src="img/logo.png" alt="Logo">
            <div class="contenuBarre">
                <center>
                    <strong>
                        <p><?php echo $htmlRechercherPar; ?></p>
                    </strong>
                </center>

                <form method="get" action="index.php">
                    <label><?php echo $htmlParProfession; ?></label>
                    <br>
                    <select name="categorie" id="categories">
                        <option value="Tout" <?php if($_GET["categorie"] == "Tout") echo 'selected="selected"'; ?>>
                            <?php echo $htmlTout; ?></option>
                        <option value="Agriculteur"
                            <?php if($_GET["categorie"] == "Agriculteur") echo 'selected="selected"'; ?>>
                            <?php echo $htmlAgriculteur; ?></option>
                        <option value="Vigneron"
                            <?php if($_GET["categorie"] == "Vigneron") echo 'selected="selected"'; ?>>
                            <?php echo $htmlVigneron; ?></option>
                        <option value="Maraîcher"
                            <?php if($_GET["categorie"] == "Maraîcher") echo 'selected="selected"'; ?>>
                            <?php echo $htmlMaraîcher; ?></option>
                        <option value="Apiculteur"
                            <?php if($_GET["categorie"] == "Apiculteur") echo 'selected="selected"'; ?>>
                            <?php echo $htmlApiculteur; ?></option>
                        <option value="Éleveur de volaille"
                            <?php if($_GET["categorie"] == "Éleveur de volaille") echo 'selected="selected"'; ?>>
                            <?php echo $htmlÉleveurdevolailles; ?></option>
                        <option value="Viticulteur"
                            <?php if($_GET["categorie"] == "Viticulteur") echo 'selected="selected"'; ?>>
                            <?php echo $htmlViticulteur; ?></option>
                        <option value="Pépiniériste"
                            <?php if($_GET["categorie"] == "Pépiniériste") echo 'selected="selected"'; ?>>
                            <?php echo $htmlPépiniériste; ?></option>
                    </select>
                    <br><br>

                    <?php echo $htmlParVille; ?>
                    <br>
                    <input type="text" name="rechercheVille" pattern="[A-Za-z0-9 ]{0,100}"
                        value="<?php echo $rechercheVille; ?>" placeholder="<?php echo $htmlVille; ?>">
                    <br>

                    <?php if (count($returnQueryAdrUti) > 0): ?>
                    <br><br>
                    <?php echo $htmlAutourDeChezMoi . ' (' . $Adr_Uti_En_Cours . ')'; ?>
                    <br><br>
                    <input name="rayon" type="range" value="<?php echo $rayon; ?>" min="1" max="100" step="1"
                        onchange="AfficheRange2(this.value)" onkeyup="AfficheRange2(this.value)">
                    <span id="monCurseurKm">
                        <?php echo $htmlRayonDe . ' ' . $rayon; if($rayon >= 100) echo '+'; ?>
                    </span>
                    <?php echo $htmlKm; ?>
                    <br><br>
                    <?php endif; ?>
                    <br>

                    <label><?php echo $htmlTri; ?></label>
                    <br>
                    <select name="tri" required>
                        <option value="nombreDeProduits"
                            <?php if($tri == "nombreDeProduits") echo 'selected="selected"'; ?>>
                            <?php echo $htmlNombreDeProduits; ?></option>
                        <option value="ordreNomAlphabétique"
                            <?php if($tri == "ordreNomAlphabétique") echo 'selected="selected"'; ?>>
                            <?php echo $htmlParNomAl; ?></option>
                        <option value="ordreNomAntiAlphabétique"
                            <?php if($tri == "ordreNomAntiAlphabétique") echo 'selected="selected"'; ?>>
                            <?php echo $htmlParNomAntiAl; ?></option>
                        <option value="ordrePrenomAlphabétique"
                            <?php if($tri == "ordrePrenomAlphabétique") echo 'selected="selected"'; ?>>
                            <?php echo $htmlParPrenomAl; ?></option>
                        <option value="ordrePrenomAntiAlphabétique"
                            <?php if($tri == "ordrePrenomAntiAlphabétique") echo 'selected="selected"'; ?>>
                            <?php echo $htmlParPrenomAntiAl; ?></option>
                    </select>
                    <br><br><br>

                    <center><input type="submit" value="<?php echo $htmlRechercher; ?>"></center>
                </form>
            </div>
        </div>

        <!-- Right column with content -->
        <div class="rightColumn">
            <!-- Top navigation banner -->
            <div class="topBanner">
                <div class="divNavigation">
                    <a class="bontonDeNavigation" href="index.php"><?php echo $htmlAccueil; ?></a>
                    <?php if (isset($_SESSION["Id_Uti"])): ?>
                    <a class="bontonDeNavigation" href="messagerie.php"><?php echo $htmlMessagerie; ?></a>
                    <a class="bontonDeNavigation" href="achats.php"><?php echo $htmlAchats; ?></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION["isProd"]) && $_SESSION["isProd"] == true): ?>
                    <a class="bontonDeNavigation" href="produits.php"><?php echo $htmlProduits; ?></a>
                    <a class="bontonDeNavigation" href="delivery.php"><?php echo $htmlCommandes; ?></a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == true): ?>
                    <a class="bontonDeNavigation" href="panel_admin.php"><?php echo $htmlPanelAdmin; ?></a>
                    <?php endif; ?>
                </div>

                <!-- Language selector form -->
                <form action="language.php" method="post" id="languageForm">
                    <select name="language" id="languagePicker" onchange="submitForm()">
                        <option value="fr" <?php if($_SESSION["language"] == "fr") echo 'selected'; ?>>Français</option>
                        <option value="en" <?php if($_SESSION["language"] == "en") echo 'selected'; ?>>English</option>
                        <option value="es" <?php if($_SESSION["language"] == "es") echo 'selected'; ?>>Español</option>
                        <option value="al" <?php if($_SESSION["language"] == "al") echo 'selected'; ?>>Deutsch</option>
                        <option value="ru" <?php if($_SESSION["language"] == "ru") echo 'selected'; ?>>русский</option>
                        <option value="ch" <?php if($_SESSION["language"] == "ch") echo 'selected'; ?>>中國人</option>
                    </select>
                </form>

                <!-- User login/info form -->
                <form method="post">
                    <input type="submit"
                        value="<?php echo !isset($_SESSION['Mail_Uti']) ? $htmlSeConnecter : $_SESSION['Mail_Uti']; ?>"
                        class="boutonDeConnection">
                    <input type="hidden" name="popup"
                        value="<?php echo isset($_SESSION['Mail_Uti']) ? 'info_perso' : 'sign_in'; ?>">
                </form>
            </div>

            <h1><?php echo $htmlProducteursEnMaj; ?></h1>

            <!-- Producer gallery -->
            <div class="gallery-container">
                <?php 
                if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["categorie"])) {
                    // Use the connection from dbConnect instead of creating a new one
                    $mabdd = dbConnect();
                    
                    // Build the query based on filters
                    if ($_GET["categorie"] == "Tout") {
                        $requete = 'SELECT UTILISATEUR.Id_Uti, PRODUCTEUR.Prof_Prod, PRODUCTEUR.Id_Prod, 
                                    UTILISATEUR.Prenom_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Adr_Uti, COUNT(PRODUIT.Id_Produit) 
                                  FROM PRODUCTEUR 
                                  JOIN UTILISATEUR ON PRODUCTEUR.Id_Uti = UTILISATEUR.Id_Uti 
                                  LEFT JOIN PRODUIT ON PRODUCTEUR.Id_Prod = PRODUIT.Id_Prod
                                  GROUP BY UTILISATEUR.Id_Uti, PRODUCTEUR.Prof_Prod, PRODUCTEUR.Id_Prod, 
                                    UTILISATEUR.Prenom_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Adr_Uti
                                  HAVING PRODUCTEUR.Prof_Prod LIKE \'%\'';
                    } else {
                        $requete = 'SELECT UTILISATEUR.Id_Uti, PRODUCTEUR.Prof_Prod, PRODUCTEUR.Id_Prod, 
                                    UTILISATEUR.Prenom_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Adr_Uti, COUNT(PRODUIT.Id_Produit) 
                                  FROM PRODUCTEUR 
                                  JOIN UTILISATEUR ON PRODUCTEUR.Id_Uti = UTILISATEUR.Id_Uti 
                                  LEFT JOIN PRODUIT ON PRODUCTEUR.Id_Prod = PRODUIT.Id_Prod
                                  GROUP BY UTILISATEUR.Id_Uti, PRODUCTEUR.Prof_Prod, PRODUCTEUR.Id_Prod, 
                                    UTILISATEUR.Prenom_Uti, UTILISATEUR.Nom_Uti, UTILISATEUR.Adr_Uti
                                  HAVING PRODUCTEUR.Prof_Prod = :categorie';
                    }
                    
                    // Add city filter if present
                    if ($rechercheVille != "") {
                        $requete .= ' AND Adr_Uti LIKE :rechercheVille';
                    }
                    
                    // Add sorting
                    switch ($tri) {
                        case "ordreNomAlphabétique":
                            $requete .= ' ORDER BY Nom_Uti ASC';
                            break;
                        case "ordreNomAntiAlphabétique":
                            $requete .= ' ORDER BY Nom_Uti DESC';
                            break;
                        case "ordrePrenomAlphabétique":
                            $requete .= ' ORDER BY Prenom_Uti ASC';
                            break;
                        case "ordrePrenomAntiAlphabétique":
                            $requete .= ' ORDER BY Prenom_Uti DESC';
                            break;
                        default:
                            $requete .= ' ORDER BY COUNT(PRODUIT.Id_Produit) DESC';
                    }
                    
                    // Prepare and execute the query
                    $stmt = $mabdd->prepare($requete);
                    
                    // Bind parameters
                    if ($_GET["categorie"] != "Tout") {
                        $stmt->bindParam(':categorie', $_GET["categorie"]);
                    }
                    
                    if ($rechercheVille != "") {
                        $villePattern = '%, _____ %' . $rechercheVille . '%';
                        $stmt->bindParam(':rechercheVille', $villePattern);
                    }
                    
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Get user coordinates for distance calculation
                    $urlUti = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($Adr_Uti_En_Cours);
                    $coordonneesUti = latLongGps($urlUti);
                    $latitudeUti = $coordonneesUti[0];
                    $longitudeUti = $coordonneesUti[1];
                    
                    // Display results
                    if (count($result) > 0) {
                        foreach ($result as $row) {
                            if ($rayon >= 100) {
                                // Display all results if radius is max
                                echo '<a href="producteur.php?Id_Prod=' . $row["Id_Prod"] . '" class="square1">';
                                echo $row["Prof_Prod"] . "<br>";
                                echo $row["Prenom_Uti"] . " " . mb_strtoupper($row["Nom_Uti"]) . "<br>";
                                echo $row["Adr_Uti"] . "<br>";
                                echo '<img src="img_producteur/' . $row["Id_Prod"] . '.png" alt="' . $htmlImageUtilisateur . '" style="width: 100%; height: 85%;">';
                                echo '</a>';
                            } else {
                                // Filter by distance
                                $urlProd = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($row["Adr_Uti"]);
                                $coordonneesProd = latLongGps($urlProd);
                                $latitudeProd = $coordonneesProd[0];
                                $longitudeProd = $coordonneesProd[1];
                                $distance = distance($latitudeUti, $longitudeUti, $latitudeProd, $longitudeProd);
                                
                                if ($distance < $rayon) {
                                    echo '<a href="producteur.php?Id_Prod=' . $row["Id_Prod"] . '" class="square1">';
                                    echo $row["Prof_Prod"] . "<br>";
                                    echo $row["Prenom_Uti"] . " " . mb_strtoupper($row["Nom_Uti"]) . "<br>";
                                    echo $row["Adr_Uti"] . "<br>";
                                    echo '<img src="img_producteur/' . $row["Id_Prod"] . '.png" alt="' . $htmlImageUtilisateur . '" style="width: 100%; height: 85%;">';
                                    echo '</a>';
                                }
                            }
                        }
                    } else {
                        echo $htmlAucunResultat;
                    }
                }
                ?>
            </div>
            <br>

            <!-- Footer links -->
            <div class="basDePage">
                <form method="post">
                    <input type="submit" value="<?php echo $htmlSignalerDys; ?>" class="lienPopup">
                    <input type="hidden" name="popup" value="contact_admin">
                </form>
                <form method="post">
                    <input type="submit" value="<?php echo $htmlCGU; ?>" class="lienPopup">
                    <input type="hidden" name="popup" value="cgu">
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    function submitForm() {
        document.getElementById("languageForm").submit();
    }

    function AfficheRange2(newVal) {
        var monCurseurKm = document.getElementById("monCurseurKm");
        if (newVal >= 100) {
            monCurseurKm.innerHTML = "<?php echo $htmlRayonDe; ?> " + newVal + "+ ";
        } else {
            monCurseurKm.innerHTML = "<?php echo $htmlRayonDe; ?> " + newVal + " ";
        }
    }
    </script>

    <?php require "popups/gestion_popups.php"; ?>
</body>

</html>