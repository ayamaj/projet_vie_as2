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
                <h1 class="m-0 text-primary"></i>Gardiens de l'océan</h1>
            </a>
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link">Accueil</a>
                    <a href="about.php" class="nav-item nav-link">Propos</a>
                    <a href="contact.php" class="nav-item nav-link ">Contactez-nous</a>
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
                <h1 class="display-2 text-white animated slideInDown mb-4">Donation</h1>
                <nav aria-label="breadcrumb animated slideInDown">
                </nav>
            </div>
        </div>
        <!-- Page Header End -->

        <!-- Contact Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <!-- contacter nous  -->
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Contactez-nous</h1>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.1s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 75px; height: 75px;">
                            <i class="fa fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <h6>4002, AKOUDA, SOUSSE</h6>
                    </div>
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.3s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 75px; height: 75px;">
                            <i class="fa fa-envelope-open fa-2x text-primary"></i>
                        </div>
                        <h6>Donation@gmail.com</h6>
                    </div>
                    <div class="col-md-6 col-lg-4 text-center wow fadeInUp" data-wow-delay="0.5s">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 75px; height: 75px;">
                            <i class="fa fa-phone-alt fa-2x text-primary"></i>
                        </div>
                        <h6>+56500896</h6>
                    </div>
                </div>
                <!--  fin contacter nous  -->

                <div class="bg-light rounded">
                    <div class="row g-0">
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                            <div class="h-100 d-flex flex-column justify-content-center p-5">
                                <?php
                                // Connexion à la base de données et traitement du formulaire
                                $servername = "localhost";
                                $username = "root"; // Remplacez par votre nom d'utilisateur MySQL
                                $password = ""; // Remplacez par votre mot de passe MySQL
                                $dbname = "aya2";

                                // Créer la connexion
                                $conn = new mysqli($servername, $username, $password, $dbname);

                                // Vérifier la connexion
                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }

                                // Variable pour stocker le message de succès
                                $success_message = "";

                                // Vérifier si le formulaire a été soumis
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    // Récupérer les données du formulaire
                                    $montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
                                    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
                                    $email = isset($_POST['email']) ? $_POST['email'] : '';
                                    $telephone = isset($_POST['telephone']) ? $_POST['telephone'] : '';
                                    $mode_paiement = isset($_POST['mode_paiement']) ? $_POST['mode_paiement'] : '';
                                    $message = isset($_POST['message']) ? $_POST['message'] : '';

                                    // Préparer et exécuter la requête SQL
                                    $stmt = $conn->prepare("INSERT INTO dons (montant, nom, email, telephone, mode_paiement, message) VALUES (?, ?, ?, ?, ?, ?)");
                                    $stmt->bind_param("dsssss", $montant, $nom, $email, $telephone, $mode_paiement, $message);

                                    if ($stmt->execute()) {
                                        $success_message = '<div class="alert alert-success">Merci pour votre don ! Votre contribution a été enregistrée avec succès.</div>';
                                    } else {
                                        $success_message = '<div class="alert alert-danger">Une erreur s\'est produite lors de l\'enregistrement de votre don. Veuillez réessayer.</div>';
                                    }

                                    $stmt->close();
                                }

                                $conn->close();
                                ?>

                                <?php if (!empty($success_message)): ?>
                                    <?php echo $success_message; ?>
                                    <a href="donation.php" class="btn btn-primary mt-3">Faire un nouveau don</a>
                                <?php else: ?>
                                    <form id="donationForm" method="POST" action="donation.php">
                                        <div class="row g-3">
                                            <!-- Montant du Don -->
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <select class="form-select border-0" id="donAmount" name="donAmount" required>
                                                        <option value="" selected disabled>Choisissez un montant</option>
                                                        <option value="10">10 €</option>
                                                        <option value="25">25 €</option>
                                                        <option value="50">50 €</option>
                                                        <option value="custom">Autre montant</option>
                                                    </select>
                                                    <label for="donAmount">Montant du Don</label>
                                                </div>
                                                <div id="customAmountContainer" class="mt-2 d-none">
                                                    <div class="form-floating">
                                                        <input type="number" class="form-control border-0" id="customAmount" name="customAmount"
                                                            placeholder="Autre montant (en €)" min="1">
                                                        <label for="customAmount">Autre Montant (€)</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="finalAmount" name="montant">
                                            </div>
                                            <!-- Nom -->
                                            <div class="col-sm-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control border-0" id="name" name="nom"
                                                        placeholder="Votre Nom" required>
                                                    <label for="name">Votre Nom</label>
                                                </div>
                                            </div>
                                            <!-- Email -->
                                            <div class="col-sm-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control border-0" id="email" name="email"
                                                        placeholder="Votre Email" required>
                                                    <label for="email">Votre Email</label>
                                                    <div id="emailError" style="color: red; display: none;">Veuillez entrer
                                                        un email valide.</div>
                                                </div>
                                            </div>

                                            <!-- Téléphone -->
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <input type="tel" class="form-control border-0" id="phone" name="telephone"
                                                        placeholder="Votre Téléphone (facultatif)" maxlength="8">
                                                    <label for="phone">Votre Téléphone (facultatif)</label>
                                                    <div id="phoneError" style="color: red; display: none;">Le numéro de
                                                        téléphone doit comporter 8 chiffres.</div>
                                                </div>
                                            </div>

                                            <!-- Mode de Paiement -->
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <select class="form-select border-0" id="paymentMethod" name="mode_paiement" required>
                                                        <option value="" selected disabled>Choisissez un mode de paiement
                                                        </option>
                                                        <option value="Carte Bancaire">Carte Bancaire</option>
                                                        <option value="PayPal">PayPal</option>
                                                        <option value="Virement Bancaire">Virement Bancaire</option>
                                                    </select>
                                                    <label for="paymentMethod">Mode de Paiement</label>
                                                </div>
                                            </div>
                                            <!-- Consentement pour recevoir des emails -->
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="consent" name="consent" required>
                                                    <label class="form-check-label" for="consent">
                                                        J'accepte de recevoir des informations de l'association.
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- Message -->
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control border-0"
                                                        placeholder="Laissez un message ici" id="message" name="message"
                                                        style="height: 100px"></textarea>
                                                    <label for="message">Message (facultatif)</label>
                                                </div>
                                            </div>
                                            <!-- Bouton -->
                                            <div class="col-12">
                                                <button class="btn btn-primary w-100 py-3" type="button"
                                                    onclick="previewForm()">Prévisualiser</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Prévisualisation -->
                                    <div class="mt-4" id="previewContainer" style="display: none;">
                                        <h4 class="mb-3">Prévisualisation</h4>
                                        <p><strong>Montant : </strong><span id="previewAmount"></span></p>
                                        <p><strong>Nom : </strong><span id="previewName"></span></p>
                                        <p><strong>Email : </strong><span id="previewEmail"></span></p>
                                        <p><strong>Téléphone : </strong><span id="previewPhone"></span></p>
                                        <p><strong>Mode de Paiement : </strong><span id="previewPayment"></span></p>
                                        <p><strong>Message : </strong><span id="previewMessage"></span></p>
                                        <button class="btn btn-success w-100" type="button" onclick="submitForm()">Confirmer
                                            et Envoyer</button>
                                    </div>
                                <?php endif; ?>

                                <script>
                                    // Gérer le montant personnalisé
                                    document.getElementById('donAmount').addEventListener('change', function () {
                                        const customContainer = document.getElementById('customAmountContainer');
                                        customContainer.classList.toggle('d-none', this.value !== 'custom');
                                    });

                                    // Prévisualiser le formulaire
                                    function previewForm() {
                                        const amount = document.getElementById('donAmount').value === 'custom' ?
                                            document.getElementById('customAmount').value + " €" :
                                            document.getElementById('donAmount').selectedOptions[0].text;
                                            
                                        const amountValue = document.getElementById('donAmount').value === 'custom' ?
                                            document.getElementById('customAmount').value :
                                            document.getElementById('donAmount').value;
                                            
                                        document.getElementById('finalAmount').value = amountValue;
                                        
                                        const name = document.getElementById('name').value;
                                        const email = document.getElementById('email').value;
                                        const phone = document.getElementById('phone').value || 'Non spécifié';
                                        const payment = document.getElementById('paymentMethod').selectedOptions[0].text;
                                        const message = document.getElementById('message').value || 'Aucun message';

                                        // Validation des champs
                                        if (!amount || !name || !email || !payment) {
                                            alert('Veuillez remplir tous les champs obligatoires.');
                                            return;
                                        }

                                        // Validation de l'email
                                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                        if (!emailRegex.test(email)) {
                                            document.getElementById('emailError').style.display = 'block';
                                            return;
                                        } else {
                                            document.getElementById('emailError').style.display = 'none';
                                        }

                                        // Validation du téléphone (8 chiffres)
                                        const phoneRegex = /^\d{8}$/;
                                        if (phone && phone !== 'Non spécifié' && !phoneRegex.test(phone)) {
                                            document.getElementById('phoneError').style.display = 'block';
                                            return;
                                        } else {
                                            document.getElementById('phoneError').style.display = 'none';
                                        }

                                        document.getElementById('previewAmount').innerText = amount;
                                        document.getElementById('previewName').innerText = name;
                                        document.getElementById('previewEmail').innerText = email;
                                        document.getElementById('previewPhone').innerText = phone;
                                        document.getElementById('previewPayment').innerText = payment;
                                        document.getElementById('previewMessage').innerText = message;

                                        document.getElementById('previewContainer').style.display = 'block';
                                    }

                                    // Soumettre le formulaire
                                    function submitForm() {
                                        document.getElementById('donationForm').submit();
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s" style="min-height: 400px;">
                            <div class="position-relative h-100">
                                <img class="position-relative rounded w-100 h-100" src="img2/oc1.jpg" frameborder="0"
                                    style="min-height: 400px; border:0;" allowfullscreen="" aria-hidden="false"
                                    tabindex="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

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
                        <a class="btn btn-link text-white-50" href="">Accueil</a>
                        <a class="btn btn-link text-white-50" href="">Propos</a>
                        <a class="btn btn-link text-white-50" href="">Contactez-nous</a>

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