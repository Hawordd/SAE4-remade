<?php
if (!isset($_SESSION)) {
    session_start();
}

spl_autoload_register(function ($class) {
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
        return true;
    }
    return false;
});

require "language_fr.php";
use DBConfig\Database;

function dbConnect(): PDO {
    return Database::getConnection();
}

$Id_Prod = isset($_GET["Id_Prod"]) ? htmlspecialchars($_GET["Id_Prod"]) : "";
$filtreType = isset($_GET["filtreType"]) ? htmlspecialchars($_GET["filtreType"]) : "TOUT";
$tri = isset($_GET["tri"]) ? htmlspecialchars($_GET["tri"]) : "No";
$rechercheNom = isset($_GET["rechercheNom"]) ? htmlspecialchars($_GET["rechercheNom"]) : "";

if(isset($_SESSION, $_SESSION['tempPopup'])) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style_general.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
</head>

<body class="d-flex" style="background-color: #EBF4EC;">
    <div class="d-flex flex-column flex-shrink-0 p-3 position-sticky top-0"
        style="width: 280px; height: 100vh; overflow-y: auto; background-color: #659D31;">
        <span class="fs-4"><a href="index.php"><img class="img-fluid mw-100" src="img/logo.png" alt="Logo"></a></span>
        <hr>
        <div class="sidebar-content text-white">
            <h5 class="text-center mb-3"><?php echo $htmlRechercherPar; ?></h5>
            <form action="producteur.php" method="get" class="needs-validation">
                <input type="hidden" name="Id_Prod" value="<?php echo $Id_Prod; ?>">

                <div class="mb-3">
                    <label for="rechercheNom" class="form-label"><?php echo $htmlTiretNom; ?></label>
                    <input type="text" class="form-control form-control-sm" id="rechercheNom" name="rechercheNom"
                        value="<?php echo $rechercheNom; ?>" placeholder="<?php echo $htmlNom; ?>">
                </div>

                <div class="mb-3">
                    <p class="form-label"><?php echo "- " .' Type de produit :'; ?></p>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeTout" value="TOUT"
                            <?php if($filtreType=="TOUT") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeTout">
                            <?php echo $htmlTout; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeAnimaux" value="ANIMAUX"
                            <?php if($filtreType=="ANIMAUX") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeAnimaux">
                            <?php echo $htmlAnimaux; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeFruits" value="FRUITS"
                            <?php if($filtreType=="FRUITS") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeFruits">
                            <?php echo $htmlFruits; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeGrains" value="GRAINS"
                            <?php if($filtreType=="GRAINS") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeGrains">
                            <?php echo $htmlGraines; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeLegumes" value="LÉGUMES"
                            <?php if($filtreType=="LÉGUMES") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeLegumes">
                            <?php echo $htmlLégumes; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typePlanches"
                            value="PLANCHES" <?php if($filtreType=="PLANCHES") echo 'checked'; ?>>
                        <label class="form-check-label" for="typePlanches">
                            <?php echo $htmlPlanches; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeViande" value="VIANDE"
                            <?php if($filtreType=="VIANDE") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeViande">
                            <?php echo $htmlViande; ?>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="filtreType" id="typeVin" value="VIN"
                            <?php if($filtreType=="VIN") echo 'checked'; ?>>
                        <label class="form-check-label" for="typeVin">
                            <?php echo $htmlVin; ?>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="tri" class="form-label"><?php echo $htmlTri; ?></label>
                    <select class="form-select form-select-sm" id="tri" name="tri">
                        <option value="No" <?php if($tri=="No") echo 'selected="selected"'; ?>>
                            <?php echo $htmlAucunTri; ?></option>
                        <option value="PrixAsc" <?php if($tri=="PrixAsc") echo 'selected="selected"'; ?>>
                            <?php echo $htmlPrixCroissant; ?></option>
                        <option value="PrixDesc" <?php if($tri=="PrixDesc") echo 'selected="selected"'; ?>>
                            <?php echo $htmlPrixDecroissant; ?></option>
                        <option value="Alpha" <?php if($tri=="Alpha") echo 'selected="selected"'; ?>>
                            <?php echo $htmlOrdreAlpha; ?></option>
                        <option value="AntiAlpha" <?php if($tri=="AntiAlpha") echo 'selected="selected"'; ?>>
                            <?php echo $htmlOrdreAntiAlpha; ?></option>
                    </select>
                </div>

                <button type="submit" class="btn btn-light w-100"><?php echo $htmlRechercher; ?></button>
            </form>
        </div>
    </div>

    <div class="flex-grow-1">
        <nav class="navbar navbar-expand-xl navbar-dark bg-white">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation"
                    style="background-color: #659D31;">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-xl-0 d-flex align-items-center">
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
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-black" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php 
                                    switch($_SESSION["language"] ?? "fr") {
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
                <?php
                $bdd = dbConnect();
                $queryInfoProd = $bdd->prepare('
                    SELECT UTILISATEUR.Id_Uti, UTILISATEUR.Adr_Uti, Prenom_Uti, Nom_Uti, Prof_Prod 
                    FROM UTILISATEUR 
                    INNER JOIN PRODUCTEUR ON UTILISATEUR.Id_Uti = PRODUCTEUR.Id_Uti 
                    WHERE PRODUCTEUR.Id_Prod = :Id_Prod
                ');
                $queryInfoProd->bindParam(":Id_Prod", $Id_Prod, PDO::PARAM_STR);
                $queryInfoProd->execute();   
                $returnQueryInfoProd = $queryInfoProd->fetchAll(PDO::FETCH_ASSOC);

                if(count($returnQueryInfoProd) == 0) {
                    echo '<div class="alert alert-danger" role="alert">Ce producteur n\'existe pas.</div>';
                    exit;
                }

                $idUti = $returnQueryInfoProd[0]["Id_Uti"];
                $address = $returnQueryInfoProd[0]["Adr_Uti"];
                $nom = $returnQueryInfoProd[0]["Nom_Uti"];
                $prenom = $returnQueryInfoProd[0]["Prenom_Uti"];
                $profession = $returnQueryInfoProd[0]["Prof_Prod"];
                ?>

                <div class="row my-4" style="width: 100dvh;">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><?php echo $prenom . ' ' . strtoupper($nom); ?></h5>
                            </div>
                            <img src="img_producteur/<?php echo $Id_Prod; ?>.png" class="card-img-top"
                                alt="<?php echo $htmlImgProducteur; ?>" style="height: 280px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $profession; ?></h6>
                                <p class="card-text"><?php echo $address; ?></p>

                                <?php if (isset($_SESSION["Id_Uti"]) && $idUti != $_SESSION["Id_Uti"]): ?>
                                <a href="messagerie.php?Id_Interlocuteur=<?php echo $idUti; ?>"
                                    class="btn btn-outline-success w-100 mb-3">
                                    <?php echo $htmlEnvoyerMessage; ?>
                                </a>
                                <?php endif; ?>

                                <?php if (isset($address)): 
                                    $addressForMap = str_replace(" ", "+", $address);
                                ?>
                                <div class="ratio ratio-16x9 mt-3">
                                    <iframe
                                        src="https://maps.google.com/maps?&q=<?php echo $addressForMap; ?>&output=embed"
                                        allowfullscreen></iframe>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><?php echo $htmlProduitsProposesDeuxPoints; ?></h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = 'SELECT Id_Produit, Id_Prod, Nom_Produit, Desc_Type_Produit, Prix_Produit_Unitaire, Nom_Unite_Prix, Qte_Produit 
                                          FROM Produits_d_un_producteur 
                                          WHERE Id_Prod = :Id_Prod';
                                
                                if ($filtreType != "TOUT") {
                                    $query .= ' AND Desc_Type_Produit = :filtreType';
                                }
                                
                                if ($rechercheNom != "") {
                                    $query .= ' AND Nom_Produit LIKE :rechercheNom';
                                }
                                
                                switch ($tri) {
                                    case "PrixAsc":
                                        $query .= ' ORDER BY Prix_Produit_Unitaire ASC';
                                        break;
                                    case "PrixDesc":
                                        $query .= ' ORDER BY Prix_Produit_Unitaire DESC';
                                        break;
                                    case "Alpha":
                                        $query .= ' ORDER BY Nom_Produit ASC';
                                        break;
                                    case "AntiAlpha":
                                        $query .= ' ORDER BY Nom_Produit DESC';
                                        break;
                                }
                                
                                $queryGetProducts = $bdd->prepare($query);
                                $queryGetProducts->bindParam(":Id_Prod", $Id_Prod, PDO::PARAM_STR);
                                
                                if ($filtreType != "TOUT") {
                                    $queryGetProducts->bindParam(":filtreType", $filtreType, PDO::PARAM_STR);
                                }
                                
                                if ($rechercheNom != "") {
                                    $rechercheParam = "%$rechercheNom%";
                                    $queryGetProducts->bindParam(":rechercheNom", $rechercheParam, PDO::PARAM_STR);
                                }
                                
                                $queryGetProducts->execute();
                                $products = $queryGetProducts->fetchAll(PDO::FETCH_ASSOC);
                                
                                if (count($products) == 0) {
                                    echo '<div class="alert alert-info" role="alert">' . $htmlAucunProduitEnStock . '</div>';
                                } else {
                                ?>

                                <form method="get" action="insert_commande.php">
                                    <input type="hidden" name="Id_Prod" value="<?php echo $Id_Prod; ?>">

                                    <div class="row row-cols-1 row-cols-md-2 g-4">
                                        <?php
                                        foreach ($products as $product) {
                                            $Id_Produit = $product["Id_Produit"];
                                            $nomProduit = $product["Nom_Produit"];
                                            $typeProduit = $product["Desc_Type_Produit"];
                                            $prixProduit = $product["Prix_Produit_Unitaire"];
                                            $QteProduit = $product["Qte_Produit"];
                                            $unitePrixProduit = $product["Nom_Unite_Prix"];
                                            
                                            if ($QteProduit > 0) {
                                        ?>
                                        <div class="col">
                                            <div class="card h-100">
                                                <div class="row g-0">
                                                    <div class="col-md-4">
                                                        <img src="img_produit/<?php echo $Id_Produit; ?>.png"
                                                            class="img-fluid rounded-start"
                                                            alt="<?php echo $htmlImageNonFournie; ?>"
                                                            style="height: 100%; object-fit: cover;">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="card-body">
                                                            <h5 class="card-title"><?php echo $nomProduit; ?></h5>
                                                            <p class="card-text">
                                                                <small
                                                                    class="text-muted"><?php echo $typeProduit; ?></small><br>
                                                                <strong><?php echo $prixProduit; ?>
                                                                    €/<?php echo $unitePrixProduit; ?></strong>
                                                            </p>
                                                            <div class="input-group mt-3">
                                                                <input type="number" class="form-control"
                                                                    name="<?php echo $Id_Produit; ?>"
                                                                    placeholder="max <?php echo $QteProduit; ?>"
                                                                    max="<?php echo $QteProduit; ?>" min="0" value="0">
                                                                <span
                                                                    class="input-group-text"><?php echo $unitePrixProduit; ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                    <?php if(isset($_SESSION["Id_Uti"]) && $idUti != $_SESSION["Id_Uti"]): ?>
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-success">
                                            <?php echo $htmlPasserCommande; ?>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function setLanguage(lang) {
        document.getElementById('languageValue').value = lang;
    }
    </script>

    <?php require "popups/gestion_popups.php"; ?>
</body>

</html>