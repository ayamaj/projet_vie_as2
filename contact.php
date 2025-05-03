<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur MySQL
$password = ""; // Remplacez par votre mot de passe MySQL
$dbname = "aya2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Création de la table si elle n'existe pas
    $sql = "CREATE TABLE IF NOT EXISTS commentaires (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        commentaire TEXT NOT NULL,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $commentaire = htmlspecialchars($_POST['commentaire'] ?? '');

    $errors = [];

    if (empty($nom)) $errors[] = "Le champ 'Nom' est requis.";
    if (empty($prenom)) $errors[] = "Le champ 'Prenom' est requis.";
    if (empty($commentaire)) $errors[] = "Le champ 'Commentaire' est requis.";

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO commentaires (nom, prenom, commentaire) VALUES (:nom, :prenom, :commentaire)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':commentaire', $commentaire);
            $stmt->execute();

            // Redirection pour éviter la soumission multiple lors du rafraîchissement
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch(PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }


    
    // Récupération des commentaires après l'insertion
    $commentaires = $conn->query("SELECT * FROM commentaires ORDER BY date_creation DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Récupération des commentaires sans insertion
    $commentaires = $conn->query("SELECT * FROM commentaires ORDER BY date_creation DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Gardiens de l'océan</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <!-- Favicon -->
    <link href="img2/icon_pro.png" rel="icon">
    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@600&family=Lobster+Two:wght@700&display=swap"
        rel="stylesheet">
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top px-4 px-lg-5 py-lg-0">
            <a href="index.php" class="navbar-brand">
                <h1 class="m-0 text-primary">Gardiens de l'océan</h1>
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link">Accueil</a>
                    <a href="about.php" class="nav-item nav-link">Propos</a>
                    <a href="contact.php" class="nav-item nav-link active">Contactez-nous</a>
                    <a href="image.php" class="nav-item nav-link">Image Océan</a>
                    <a href="admin_dashboard.php" class="nav-item nav-link">Authentification</a>
                </div>
                <a href="donation.php" class="btn btn-primary rounded-pill px-3 d-none d-lg-block">Donation<i
                        class="fa fa-arrow-right ms-3"></i></a>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Page Header End -->
        <div class="container-xxl py-5 page-header position-relative mb-5">
            <div class="container py-5">
                <h1 class="display-2 text-white animated slideInDown mb-4">Status </h1>
            </div>
        </div>
        <!-- Page Header End -->

        <!-- status -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded">
                    <div class="row g-0">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="h-100 d-flex flex-column justify-content-center p-5">
                                <h1 class="mb-4">Commentaire </h1>
                                <form id="myForm" method="POST">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="nom" name="nom"
                                                    placeholder="Nom" >
                                                <label for="nom">Nom</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control border-0" id="prenom" name="prenom"
                                                    placeholder="Prenom" >
                                                <label for="prenom">Prenom</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control border-0" name="commentaire"
                                                    placeholder="Leave a commentaire here" id="commentaire"
                                                    style="height: 100px"></textarea>
                                                <label for="commentaire">Commentaire</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 py-3" type="submit">Soumettre</button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Conteneur d'alerte -->
                                <div id="alertBox" class="alert">
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert error">
                                            <?= implode('<br>', $errors) ?>
                                        </div>
                                    <?php elseif (isset($success)): ?>
                                        <div class="alert success">
                                            <?= $success ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <script>
                                    document.getElementById('myForm').addEventListener('submit', function (event) {
                                        event.preventDefault(); // Empêche la soumission du formulaire immédiatement
                                
                                        let nom = document.getElementById('nom').value.trim();
                                        let prenom = document.getElementById('prenom').value.trim();
                                        let commentaire = document.getElementById('commentaire').value.trim();
                                        let errors = [];
                                
                                        // Vérifications des champs
                                        if (nom === '') {
                                            errors.push("Le champ 'Nom' est requis.");
                                        }
                                        if (prenom === '') {
                                            errors.push("Le champ 'Prenom' est requis.");
                                        }
                                        if (commentaire === '') {
                                            errors.push("Le champ 'Commentaire' est requis.");
                                        }
                                
                                        let alertBox = document.getElementById('alertBox');
                                
                                        if (errors.length > 0) {
                                            alertBox.className = 'alert error'; // Ajout du style d'erreur
                                            alertBox.innerHTML = errors.join("<br>"); // Affiche les erreurs
                                            alertBox.style.display = 'block'; // Affiche l'alerte
                                        } else {
                                            alertBox.className = 'alert success'; // Ajout du style de succès
                                            alertBox.innerHTML = "Formulaire soumis avec succès !"; // Affiche le message de succès
                                            alertBox.style.display = 'block'; // Affiche l'alerte
                                
                                            // Soumettre le formulaire après 1 seconde
                                            setTimeout(function() {
                                                document.getElementById('myForm').submit();
                                            }, 1000);
                                        }
                                    });
                                </script>
                                
                                <style>
                                    /* Style de la boîte d'alerte */
                                    .alert {
                                        display: none;
                                        /* Cacher par défaut */
                                        padding: 15px;
                                        margin-top: 20px;
                                        border-radius: 5px;
                                        font-size: 16px;
                                        color: white;
                                        width: 100%;
                                        max-width: 500px;
                                        margin-left: auto;
                                        margin-right: auto;
                                    }

                                    .alert.success {
                                        background-color: #4CAF50;
                                        /* Vert pour succès */
                                        display: block;
                                    }

                                    .alert.error {
                                        background-color: #ec5d5382;
                                        /* Rouge pour erreur */
                                        display: block;
                                    }
                                </style>
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s" style="min-height: 400px;">
                            <div class="position-relative h-100">
                                <img class="position-absolute w-100 h-100 rounded" src="img2/oc1.jpg"
                                    style="object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin status  -->

        <!-- gens status  -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Commentaires récents</h1>
                </div>
                <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <?php if (!empty($commentaires)): ?>
                        <?php foreach ($commentaires as $commentaire): ?>
                            <div class="testimonial-item bg-light rounded p-5">
                                <p class="fs-5"><?= htmlspecialchars($commentaire['commentaire']) ?></p>
                                <div class="d-flex align-items-center bg-white me-n5" style="border-radius: 50px 0 0 50px;">
                                    <img class="img-fluid flex-shrink-0 rounded-circle" src="img2/testimonial-<?= rand(1,3) ?>.jpg"
                                        style="width: 90px; height: 90px;">
                                    <div class="ps-3">
                                        <h3 class="mb-1"><?= htmlspecialchars($commentaire['prenom']) ?> <?= htmlspecialchars($commentaire['nom']) ?></h3>
                                        <span><?= date('d/m/Y', strtotime($commentaire['date_creation'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <br> 
                        <?php endforeach;  ?> 
                    <?php else: ?>
                        <div class="testimonial-item bg-light rounded p-5">
                            <p class="fs-5">Aucun commentaire pour le moment. Soyez le premier à commenter!</p>
                        </div>
                    <?php endif; ?>
                </div>
                <br>
            </div>
        </div>
        <!--  fin gens status  -->

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-4 col-md-6">
                        <h3 class="text-white mb-4">Contactez-nous</h3>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>4002 SOUSSE, AKOUDA</p>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+56200380</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>Gardiens@gmail.com</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h3 class="text-white mb-4">Lien</h3>
                        <a class="btn btn-link text-white-50" href="index.php">Accueil</a>
                        <a class="btn btn-link text-white-50" href="about.php">Propos</a>
                        <a class="btn btn-link text-white-50" href="contact.php">Contactez-nous</a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h3 class="text-white mb-4">Image</h3>
                        <div class="row g-2 pt-2">
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/net1.webp" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/net2.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/net3.png" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/oc1.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/oc2.jpg" alt="">
                            </div>
                            <div class="col-4">
                                <img class="img-fluid rounded bg-light p-1" src="img2/net1.webp" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <?= date('Y') ?> Gardiens de l'océan, Tous droits réservés.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>