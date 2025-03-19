<?php
// Start session at the very beginning
if (!isset($_SESSION)) {
    session_start();
}

spl_autoload_register(function ($class) {
    // Convert namespace separator to directory separator
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($path)) {
        require_once $path;
        return true;
    }
    return false;
});

// Include language file
require "language.php";

//Use database classes namespace
use DBConfig\Database;

// Database connection function
function dbConnect(): PDO
{
    return Database::getConnection();
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
    curl_setopt($ch, CURLOPT_PROXY, 'proxy.univ-lemans.fr');
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $htmlMarque; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body class="d-flex" style="background-color: #EBF4EC;">
    <!-- Sidebar -->
    <div class="d-flex flex-column flex-shrink-0 p-3 position-sticky top-0"
        style="width: 280px; height: 100vh; overflow-y: auto; background-color: #659D31;">
        <span class="fs-4"><img class="img-fluid mw-100" src="img/logo.png" alt="Logo"></span>
        <hr>
        <div class="sidebar-content text-white">
            <h5 class="text-center mb-3"><?php echo $htmlRechercherPar; ?></h5>
            <form method="get" action="index.php" class="needs-validation">
                <!-- Profession -->
                <div class="mb-3">
                    <label for="categories" class="form-label"><?php echo $htmlParProfession; ?></label>
                    <select class="form-select form-select-sm" name="categorie" id="categories">
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
                </div>

                <!-- Ville -->
                <div class="mb-3">
                    <label for="ville" class="form-label"><?php echo $htmlParVille; ?></label>
                    <input type="text" class="form-control form-control-sm" id="ville" name="rechercheVille"
                        pattern="[A-Za-z0-9 ]{0,100}" value="<?php echo $rechercheVille; ?>"
                        placeholder="<?php echo $htmlVille; ?>">
                </div>

                <!-- Rayon -->
                <?php if (count($returnQueryAdrUti) > 0): ?>
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <?php echo $htmlAutourDeChezMoi . ' (' . $Adr_Uti_En_Cours . ')'; ?>
                    </label>
                    <div class="range">
                        <input type="range" class="form-range" name="rayon" id="rayonRange"
                            value="<?php echo $rayon; ?>" min="1" max="100" step="1"
                            onchange="AfficheRange2(this.value)" onkeyup="AfficheRange2(this.value)">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="monCurseurKm" class="small">
                                <?php echo $htmlRayonDe . ' ' . $rayon; if($rayon >= 100) echo '+'; ?>
                                <?php echo $htmlKm; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tri -->
                <div class="mb-4">
                    <label for="tri" class="form-label"><?php echo $htmlTri; ?></label>
                    <select class="form-select form-select-sm" name="tri" required>
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
                </div>
                <button type="submit" class="btn btn-light w-100"><?php echo $htmlRechercher; ?></button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-xl navbar-dark bg-white">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation"
                    style="background-color: #659D31;">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-xl-0 d-flex align-items-center">
                        <!-- Always visible -->
                        <li class="nav-item">
                            <a class="nav-link text-black" href="index.php"><?php echo $htmlAccueil; ?></a>
                        </li>

                        <?php if (isset($_SESSION["Id_Uti"])): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="messagerie.php"><?php echo $htmlMessagerie; ?></a>
                        </li>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="achats.php"><?php echo $htmlAchats; ?></a>
                        </li>

                        <?php if (isset($_SESSION["isProd"]) && $_SESSION["isProd"] == true): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="produits.php"><?php echo $htmlProduits; ?></a>
                        </li>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="delivery.php"><?php echo $htmlCommandes; ?></a>
                        </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == true): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="panel_admin.php"><?php echo $htmlPanelAdmin; ?></a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav d-flex align-items-center">
                        <!-- Language selector form -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-black" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    switch($_SESSION["language"]) {
                                        case "fr": echo "🇫🇷 Français"; break;
                                        case "en": echo "🇬🇧 English"; break;
                                        case "es": echo "🇪🇸 Español"; break;
                                        case "al": echo "🇩🇪 Deutsch"; break;
                                        case "ru": echo "🇷🇺 русский"; break;
                                        case "ch": echo "🇨🇳 中國人"; break;
                                        default: echo "🇫🇷 Français";
                                    }
                                ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <form action="language.php" method="post" id="languageForm">
                                        <input type="hidden" id="languageValue" name="language" value="fr">
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('fr')">🇫🇷
                                            Français</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('en')">🇬🇧
                                            English</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('es')">🇪🇸
                                            Español</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('al')">🇩🇪
                                            Deutsch</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('ru')">🇷🇺
                                            русский</button>
                                        <button type="submit" class="dropdown-item" onclick="setLanguage('ch')">🇨🇳
                                            中國人</button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <?php if (isset($_SESSION['Mail_Uti'])): ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <form method="post">
                                <input type="hidden" name="popup" value="info_perso">
                                <button type="submit" class="nav-link text-black border-0 bg-transparent">
                                    <?php echo $_SESSION['Mail_Uti']; ?>
                                </button>
                            </form>
                        </li>
                        <?php else: ?>
                        <li class="vr" style="background-color: #659D31; width: 2px; height: 40px;"></li>
                        <li class="nav-item">
                            <form method="post">
                                <input type="hidden" name="popup" value="sign_in">
                                <button type="submit" class="btn btn-success">
                                    <?php echo $htmlSeConnecter; ?>
                                </button>
                            </form>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-3">
            <div class="container">
                <h1 class="my-3 text-center"><?php echo $htmlProducteursEnMaj; ?></h1>


                <!-- Producer gallery using Bootstrap cards -->
                <div class="row">
                    <?php 
                    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["categorie"])) {
                        // Use the connection from dbConnect
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
                    
                        if (count($result) > 0) {
                            foreach ($result as $row) {
                                if ($rayon >= 100) {
                                    // Display all results if radius is max
                                    echo '<div class="col-md-4 mb-4">';
                                    echo '<a href="producteur.php?Id_Prod=' . $row["Id_Prod"] . '" class="text-decoration-none">';
                                    echo '<div class="card shadow-sm h-100">';
                                    echo '<div class="card-header bg-success text-white text-center">';
                                    echo '<h5 class="mb-0">' . $row["Prenom_Uti"] . ' ' . mb_strtoupper($row["Nom_Uti"]) . '</h5>';
                                    echo '</div>';
                                    echo '<img src="img_producteur/' . $row["Id_Prod"] . '.png" class="card-img-top producer-image" alt="' . $htmlImageUtilisateur . '" style="height: 200px; object-fit: cover;">';
                                    echo '<div class="card-body text-center">';
                                    echo '<h6 class="card-title">' . $row["Prof_Prod"] . '</h6>';
                                    echo '<p class="card-text">' . $row["Adr_Uti"] . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                } else {
                                    // Filter by distance
                                    $urlProd = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($row["Adr_Uti"]);
                                    $coordonneesProd = latLongGps($urlProd);
                                    $latitudeProd = $coordonneesProd[0];
                                    $longitudeProd = $coordonneesProd[1];
                                    $distance = distance($latitudeUti, $longitudeUti, $latitudeProd, $longitudeProd);
                                    
                                    if ($distance < $rayon) {
                                        echo '<div class="col-md-4 mb-4">';
                                        echo '<a href="producteur.php?Id_Prod=' . $row["Id_Prod"] . '" class="text-decoration-none">';
                                        echo '<div class="card shadow-sm h-100">';
                                        echo '<div class="card-header bg-success text-white text-center">';
                                        echo '<h5 class="mb-0">' . $row["Prenom_Uti"] . ' ' . mb_strtoupper($row["Nom_Uti"]) . '</h5>';
                                        echo '</div>';
                                        echo '<img src="img_producteur/' . $row["Id_Prod"] . '.png" class="card-img-top producer-image" alt="' . $htmlImageUtilisateur . '" style="height: 200px; object-fit: cover;">';
                                        echo '<div class="card-body text-center">';
                                        echo '<h6 class="card-title">' . $row["Prof_Prod"] . '</h6>';
                                        echo '<p class="card-text">' . $row["Adr_Uti"] . '</p>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                }
                            }
                        } else {
                            echo '<div class="col-12 text-center"><p>' . $htmlAucunResultat . '</p></div>';
                        }
                    }
                    ?>
                </div>
            </div>

            <!-- Footer links -->
            <footer class="container-fluid mt-5 py-3 bg-light">
                <div class="row justify-content-center">
                    <div class="col-auto mx-3">
                        <form method="post">
                            <input type="hidden" name="popup" value="contact_admin">
                            <button type="submit"
                                class="btn btn-outline-secondary btn-sm"><?php echo $htmlSignalerDys; ?></button>
                        </form>
                    </div>
                    <div class="col-auto mx-3">
                        <form method="post">
                            <input type="hidden" name="popup" value="cgu">
                            <button type="submit"
                                class="btn btn-outline-secondary btn-sm"><?php echo $htmlCGU; ?></button>
                        </form>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script>
    function setLanguage(lang) {
        document.getElementById('languageValue').value = lang;
    }

    function AfficheRange2(newVal) {
        var monCurseurKm = document.getElementById("monCurseurKm");
        if (newVal >= 100) {
            monCurseurKm.innerHTML = "<?php echo $htmlRayonDe; ?> " + newVal + "+ <?php echo $htmlKm; ?>";
        } else {
            monCurseurKm.innerHTML = "<?php echo $htmlRayonDe; ?> " + newVal + " <?php echo $htmlKm; ?>";
        }
    }
    </script>

    <?php require "popups/gestion_popups.php"; ?>
</body>

</html>