<?php
// Start session at the very beginning
if (!isset($_SESSION)) {
    session_start();
}

// Check if user is logged in, redirect if not
if (!isset($_SESSION["Id_Uti"])) {
    header("Location: index.php");
    exit;
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
function dbConnect(): PDO {
    return Database::getConnection();
}

// Get user ID from session
$utilisateur = htmlspecialchars($_SESSION["Id_Uti"]);

// Process category filter
$filtreCategorie = isset($_POST["typeCategorie"]) ? htmlspecialchars($_POST["typeCategorie"]) : "0";

// Handle popup session management
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
        <span class="fs-4"><a href="index.php"><img class="img-fluid mw-100" src="img/logo.png" alt="Logo"></a></span>
        <hr>
        <div class="sidebar-content text-white">
            <h5 class="text-center mb-3"><?php echo $htmlFiltrerParDeuxPoints; ?></h5>
            <form action="achats.php" method="post" class="needs-validation">
                <p class="form-label"><?php echo $htmlStatut; ?></p>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="typeCategorie" id="categorieTout" value="0"
                        <?php if($filtreCategorie=="0") echo 'checked'; ?>>
                    <label class="form-check-label" for="categorieTout">
                        <?php echo $htmlTOUT; ?>
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="typeCategorie" id="categorieEnCours" value="1"
                        <?php if($filtreCategorie=="1") echo 'checked'; ?>>
                    <label class="form-check-label" for="categorieEnCours">
                        <?php echo $htmlENCOURS; ?>
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="typeCategorie" id="categoriePrete" value="2"
                        <?php if($filtreCategorie=="2") echo 'checked'; ?>>
                    <label class="form-check-label" for="categoriePrete">
                        <?php echo $htmlPRETE; ?>
                    </label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="typeCategorie" id="categorieLivree" value="4"
                        <?php if($filtreCategorie=="4") echo 'checked'; ?>>
                    <label class="form-check-label" for="categorieLivree">
                        <?php echo $htmlLIVREE; ?>
                    </label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="radio" name="typeCategorie" id="categorieAnnulee" value="3"
                        <?php if($filtreCategorie=="3") echo 'checked'; ?>>
                    <label class="form-check-label" for="categorieAnnulee">
                        <?php echo $htmlANNULEE; ?>
                    </label>
                </div>

                <button type="submit" class="btn btn-light w-100"><?php echo $htmlFiltrer; ?></button>
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
                            <a class="nav-link text-black active" href="achats.php"
                                aria-current="page"><?php echo $htmlAchats; ?></a>
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
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="p-3">
            <div class="container">
                <h1 class="my-3"><?php echo $htmlAchats; ?></h1>

                <?php
                // Database connection
                $bdd = dbConnect();
                
                // Fetch orders
                $query = 'SELECT PRODUCTEUR.Id_Uti, Desc_Statut, Id_Commande, Nom_Uti, Prenom_Uti, Adr_Uti, COMMANDE.Id_Statut 
                          FROM COMMANDE 
                          INNER JOIN PRODUCTEUR ON COMMANDE.Id_Prod=PRODUCTEUR.Id_Prod 
                          INNER JOIN info_producteur ON COMMANDE.Id_Prod=info_producteur.Id_Prod 
                          INNER JOIN STATUT ON COMMANDE.Id_Statut=STATUT.Id_Statut 
                          WHERE COMMANDE.Id_Uti= :utilisateur';
                
                if ($filtreCategorie != "0") {
                    $query .= ' AND COMMANDE.Id_Statut= :filtreCategorie';
                }
                
                $query .= ' ORDER BY Id_Commande DESC';
                
                $queryGetCommande = $bdd->prepare($query);
                $queryGetCommande->bindParam(":utilisateur", $utilisateur, PDO::PARAM_STR);
                
                if ($filtreCategorie != "0") {
                    $queryGetCommande->bindParam(":filtreCategorie", $filtreCategorie, PDO::PARAM_STR);
                }
                
                $queryGetCommande->execute();
                $returnQueryGetCommande = $queryGetCommande->fetchAll(PDO::FETCH_ASSOC);
                
                // Display results
                if (count($returnQueryGetCommande) == 0 && $filtreCategorie == "0") {
                    // No orders at all
                ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $htmlAucuneCommande; ?>
                </div>
                <div class="d-grid gap-2 col-md-6 mx-auto">
                    <a href="index.php" class="btn btn-success">
                        <?php echo $htmlDecouverteProducteurs; ?>
                    </a>
                </div>
                <?php
                } elseif (count($returnQueryGetCommande) == 0) {
                    // No orders matching filter criteria
                ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $htmlAucuneCommandeCorrespondCriteres; ?>
                </div>
                <?php
                } else {
                    // Display orders
                    foreach ($returnQueryGetCommande as $commande) {
                        $Id_Commande = $commande["Id_Commande"];
                        $Nom_Prod = mb_strtoupper($commande["Nom_Uti"]);
                        $Prenom_Prod = $commande["Prenom_Uti"];
                        $Adr_Uti = $commande["Adr_Uti"];
                        $Desc_Statut = mb_strtoupper($commande["Desc_Statut"]);
                        $Id_Statut = $commande["Id_Statut"];
                        $idUti = $commande["Id_Uti"];
                        
                        // Fetch order products
                        $queryGetProduitCommande = $bdd->prepare('
                            SELECT Nom_Produit, Qte_Produit_Commande, Prix_Produit_Unitaire, Nom_Unite_Prix 
                            FROM produits_commandes 
                            WHERE Id_Commande = :Id_Commande
                        ');
                        $queryGetProduitCommande->bindParam(":Id_Commande", $Id_Commande, PDO::PARAM_STR);
                        $queryGetProduitCommande->execute();
                        $returnQueryGetProduitCommande = $queryGetProduitCommande->fetchAll(PDO::FETCH_ASSOC);
                        $nbProduit = count($returnQueryGetProduitCommande);
                        
                        if ($nbProduit > 0) {
                            $total = 0;
                ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                        <div>
                            <h5 class="card-title mb-0">
                                <?php echo $htmlCommandeNum; ?> <?php echo $Id_Commande; ?> -
                                <?php echo $htmlChez; ?> <?php echo $Prenom_Prod . ' ' . $Nom_Prod; ?>
                            </h5>
                            <p class="text-muted mb-0"><?php echo $Adr_Uti; ?></p>
                        </div>
                        <span class="badge <?php 
                                        switch($Id_Statut) {
                                            case 1: echo 'bg-primary'; break; // En cours
                                            case 2: echo 'bg-warning'; break; // Prête
                                            case 3: echo 'bg-danger'; break;  // Annulée
                                            case 4: echo 'bg-success'; break; // Livrée
                                            default: echo 'bg-secondary';
                                        }
                                    ?>"><?php echo $Desc_Statut; ?></span>
                    </div>

                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <?php if ($Id_Statut != 3 && $Id_Statut != 4): ?>
                            <form action="delete_commande.php" method="post">
                                <input type="hidden" name="deleteValeur" value="<?php echo $Id_Commande; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <?php echo $htmlAnnulerCommande; ?>
                                </button>
                            </form>
                            <?php endif; ?>

                            <a href="messagerie.php?Id_Interlocuteur=<?php echo $idUti; ?>"
                                class="btn btn-outline-success btn-sm">
                                <?php echo $htmlEnvoyerMessage; ?>
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $htmlProduit; ?></th>
                                        <th><?php echo $htmlQuantite; ?></th>
                                        <th><?php echo $htmlUnitePrix; ?></th>
                                        <th><?php echo $htmlPrixUnitaire; ?></th>
                                        <th><?php echo $htmlTotal; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returnQueryGetProduitCommande as $produit): 
                                                    $Nom_Produit = $produit["Nom_Produit"];
                                                    $Qte_Produit_Commande = $produit["Qte_Produit_Commande"];
                                                    $Nom_Unite_Prix = $produit["Nom_Unite_Prix"];
                                                    $Prix_Produit_Unitaire = $produit["Prix_Produit_Unitaire"];
                                                    $sousTotal = floatval($Prix_Produit_Unitaire) * intval($Qte_Produit_Commande);
                                                    $total += $sousTotal;
                                                ?>
                                    <tr>
                                        <td><?php echo $Nom_Produit; ?></td>
                                        <td><?php echo $Qte_Produit_Commande; ?></td>
                                        <td><?php echo $Nom_Unite_Prix; ?></td>
                                        <td><?php echo $Prix_Produit_Unitaire; ?> €</td>
                                        <td><?php echo number_format($sousTotal, 2); ?> €</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end"><?php echo $htmlTotalDeuxPoints; ?></th>
                                        <th><?php echo number_format($total, 2); ?> €</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    }
                }
                ?>
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
    </script>

    <?php require "popups/gestion_popups.php"; ?>
</body>

</html>