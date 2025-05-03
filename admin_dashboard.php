<?php
session_start();

// Empêcher la mise en cache
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aya2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement de la suppression d'un don
if (isset($_GET['delete_don'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM dons WHERE id = :id");
        $stmt->bindParam(':id', $_GET['delete_don']);
        $stmt->execute();
        
        header('Location: admin_dashboard.php?tab=dons&success=don_deleted');
        exit;
    } catch(PDOException $e) {
        header('Location: admin_dashboard.php?tab=dons&error=delete_failed');
        exit;
    }
}

// Traitement de la suppression d'un commentaire
if (isset($_GET['delete_comment'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM commentaires WHERE id = :id");
        $stmt->bindParam(':id', $_GET['delete_comment']);
        $stmt->execute();
        
        header('Location: admin_dashboard.php?tab=commentaires&success=comment_deleted');
        exit;
    } catch(PDOException $e) {
        header('Location: admin_dashboard.php?tab=commentaires&error=delete_failed');
        exit;
    }
}

// Traitement de la modification d'un don
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_don'])) {
    try {
        $stmt = $conn->prepare("UPDATE dons SET 
            montant = :montant, 
            nom = :nom, 
            email = :email, 
            telephone = :telephone, 
            mode_paiement = :mode_paiement, 
            message = :message 
            WHERE id = :id");
        
        $stmt->bindParam(':id', $_POST['don_id']);
        $stmt->bindParam(':montant', $_POST['montant']);
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':telephone', $_POST['telephone']);
        $stmt->bindParam(':mode_paiement', $_POST['mode_paiement']);
        $stmt->bindParam(':message', $_POST['message']);
        
        $stmt->execute();
        
        header('Location: admin_dashboard.php?tab=dons&success=don_updated');
        exit;
    } catch(PDOException $e) {
        header('Location: admin_dashboard.php?tab=dons&error=update_failed');
        exit;
    }
}

// Traitement de la modification d'un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    try {
        $stmt = $conn->prepare("UPDATE commentaires SET 
            nom = :nom, 
            prenom = :prenom, 
            commentaire = :commentaire 
            WHERE id = :id");
        
        $stmt->bindParam(':id', $_POST['comment_id']);
        $stmt->bindParam(':nom', $_POST['nom']);
        $stmt->bindParam(':prenom', $_POST['prenom']);
        $stmt->bindParam(':commentaire', $_POST['commentaire']);
        
        $stmt->execute();
        
        header('Location: admin_dashboard.php?tab=commentaires&success=comment_updated');
        exit;
    } catch(PDOException $e) {
        header('Location: admin_dashboard.php?tab=commentaires&error=update_failed');
        exit;
    }
}

// Récupérer les données pour l'édition
$don_to_edit = null;
if (isset($_GET['edit_don'])) {
    $stmt = $conn->prepare("SELECT * FROM dons WHERE id = :id");
    $stmt->bindParam(':id', $_GET['edit_don']);
    $stmt->execute();
    $don_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$comment_to_edit = null;
if (isset($_GET['edit_comment'])) {
    $stmt = $conn->prepare("SELECT * FROM commentaires WHERE id = :id");
    $stmt->bindParam(':id', $_GET['edit_comment']);
    $stmt->execute();
    $comment_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Déterminer l'onglet actif
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dons';

// Récupérer les dons
$dons = $conn->query("SELECT * FROM dons ORDER BY date_don DESC")->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total des dons
$total_dons = 0;
foreach ($dons as $don) {
    $total_dons += $don['montant'];
}

// Récupérer les commentaires
$commentaires = $conn->query("SELECT * FROM commentaires ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - Gardiens de l'océan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --ocean-blue: #1a4b8c;
            --coral-mauve: #d17b88;
            --light-blue: #e6f2ff;
            --deep-blue: #0a2d5a;
            --sea-green: #2a9d8f;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--ocean-blue), var(--deep-blue));
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
            margin: 5px 10px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: var(--coral-mauve);
            font-weight: 600;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .tab-content {
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .total-dons {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--ocean-blue);
            background-color: var(--light-blue);
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .btn-coral {
            background-color: var(--coral-mauve);
            border-color: var(--coral-mauve);
            color: white;
        }
        
        .btn-coral:hover {
            background-color: #c06c7a;
            border-color: #c06c7a;
            color: white;
        }
        
        .btn-ocean {
            background-color: var(--ocean-blue);
            border-color: var(--ocean-blue);
            color: white;
        }
        
        .btn-ocean:hover {
            background-color: var(--deep-blue);
            border-color: var(--deep-blue);
            color: white;
        }
        
        .table thead {
            background: linear-gradient(to right, var(--ocean-blue), var(--sea-green));
            color: white;
        }
        
        .table th {
            border: none;
            padding: 12px 15px;
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(230, 242, 255, 0.3);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(26, 75, 140, 0.1);
        }
        
        .alert-success {
            background-color: rgba(42, 157, 143, 0.2);
            border-color: var(--sea-green);
            color: var(--deep-blue);
        }
        
        .alert-danger {
            background-color: rgba(209, 123, 136, 0.2);
            border-color: var(--coral-mauve);
            color: #8a2a3a;
        }
        
        .main-header {
            border-bottom: 2px solid rgba(26, 75, 140, 0.1);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .page-title {
            color: var(--ocean-blue);
            font-weight: 700;
            position: relative;
            display: inline-block;
        }
        
        .page-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--coral-mauve);
        }
        
        .modal-header {
            background: var(--ocean-blue);
            color: white;
        }
        
        .modal-footer .btn-danger {
            background-color: var(--coral-mauve);
            border-color: var(--coral-mauve);
        }
        
        .btn-edit {
            background-color: var(--sea-green);
            border-color: var(--sea-green);
            color: white;
            margin-right: 5px;
        }
        
        .btn-edit:hover {
            background-color: #238a7d;
            border-color: #238a7d;
            color: white;
        }
        
        .image-preview-container {
            margin-top: 10px;
            display: none;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .current-image {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Modal de suppression de don -->
    <div class="modal fade" id="deleteDonModal" tabindex="-1" aria-labelledby="deleteDonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDonModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce don ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="confirmDeleteDon" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression de commentaire -->
    <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCommentModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="confirmDeleteComment" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de don -->
    <div class="modal fade" id="editDonModal" tabindex="-1" aria-labelledby="editDonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDonModalLabel"><i class="fas fa-edit me-2"></i>Modifier un don</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="admin_dashboard.php">
                    <div class="modal-body">
                        <input type="hidden" name="edit_don" value="1">
                        <input type="hidden" name="don_id" id="editDonId">
                        
                        <div class="mb-3">
                            <label for="editMontant" class="form-label">Montant (€)</label>
                            <input type="number" step="0.01" class="form-control" id="editMontant" name="montant" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editNom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="editNom" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email">
                        </div>
                        
                        <div class="mb-3">
                            <label for="editTelephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="editTelephone" name="telephone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="editModePaiement" class="form-label">Mode de paiement</label>
                            <select class="form-select" id="editModePaiement" name="mode_paiement" required>
                                <option value="Carte bancaire">Carte bancaire</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Virement">Virement</option>
                                <option value="Chèque">Chèque</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="editMessage" name="message" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de commentaire -->
    <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCommentModalLabel"><i class="fas fa-edit me-2"></i>Modifier un commentaire</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="admin_dashboard.php">
                    <div class="modal-body">
                        <input type="hidden" name="edit_comment" value="1">
                        <input type="hidden" name="comment_id" id="editCommentId">
                        
                        <div class="mb-3">
                            <label for="editNomComment" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="editNomComment" name="nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editPrenomComment" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="editPrenomComment" name="prenom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editCommentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="editCommentaire" name="commentaire" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar p-0">
                <div class="sidebar-header text-center">
                    <h4 class="text-white mb-1">Gardiens de l'océan</h4>
                    <p class="text-white-50 mb-0">Administration</p>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link <?= $active_tab === 'dons' ? 'active' : '' ?>" href="admin_dashboard.php?tab=dons">
                            <i class="fas fa-euro-sign"></i> Dons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $active_tab === 'commentaires' ? 'active' : '' ?>" href="admin_dashboard.php?tab=commentaires">
                            <i class="fas fa-comments"></i> Commentaires
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a class="nav-link btn-coral mx-2 text-center" href="admin_logout.php">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center main-header">
                    <h1 class="page-title">Tableau de bord</h1>
                </div>

                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php
                    if ($_GET['error'] === 'delete_failed') {
                        echo "<i class='fas fa-exclamation-circle me-2'></i> Une erreur est survenue lors de la suppression.";
                    } elseif ($_GET['error'] === 'add_failed') {
                        echo "<i class='fas fa-exclamation-circle me-2'></i> Une erreur est survenue lors de l'ajout.";
                    } elseif ($_GET['error'] === 'update_failed') {
                        echo "<i class='fas fa-exclamation-circle me-2'></i> Une erreur est survenue lors de la mise à jour.";
                    } elseif ($_GET['error'] === 'image_upload_failed') {
                        echo "<i class='fas fa-exclamation-circle me-2'></i> Une erreur est survenue lors du téléchargement de l'image.";
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php
                    if ($_GET['success'] === 'don_deleted') {
                        echo "<i class='fas fa-check-circle me-2'></i> Le don a été supprimé avec succès.";
                    } elseif ($_GET['success'] === 'comment_deleted') {
                        echo "<i class='fas fa-check-circle me-2'></i> Le commentaire a été supprimé avec succès.";
                    } elseif ($_GET['success'] === 'don_updated') {
                        echo "<i class='fas fa-check-circle me-2'></i> Le don a été modifié avec succès.";
                    } elseif ($_GET['success'] === 'comment_updated') {
                        echo "<i class='fas fa-check-circle me-2'></i> Le commentaire a été modifié avec succès.";
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Onglet Dons -->
                    <div class="tab-pane fade <?= $active_tab === 'dons' ? 'show active' : '' ?>" id="dons">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-ocean"><i class="fas fa-euro-sign me-2"></i>Dons reçus</h3>
                            <div class="total-dons">
                                <i class="fas fa-coins me-2"></i>Total : <?= number_format($total_dons, 2) ?> €
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="donsTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Montant (€)</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Paiement</th>
                                        <th>Date</th>
                                        <th>Message</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dons as $don): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($don['id']) ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($don['montant']) ?> €</td>
                                        <td><?= htmlspecialchars($don['nom']) ?></td>
                                        <td><?= htmlspecialchars($don['email']) ?></td>
                                        <td><?= htmlspecialchars($don['telephone']) ?></td>
                                        <td><span class="badge bg-primary"><?= htmlspecialchars($don['mode_paiement']) ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></td>
                                        <td><?= htmlspecialchars($don['message']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-edit edit-don-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editDonModal"
                                                    data-id="<?= $don['id'] ?>"
                                                    data-montant="<?= $don['montant'] ?>"
                                                    data-nom="<?= htmlspecialchars($don['nom']) ?>"
                                                    data-email="<?= htmlspecialchars($don['email']) ?>"
                                                    data-telephone="<?= htmlspecialchars($don['telephone']) ?>"
                                                    data-mode-paiement="<?= htmlspecialchars($don['mode_paiement']) ?>"
                                                    data-message="<?= htmlspecialchars($don['message']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-don-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteDonModal"
                                                    data-id="<?= $don['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Onglet Commentaires -->
                    <div class="tab-pane fade <?= $active_tab === 'commentaires' ? 'show active' : '' ?>" id="commentaires">
                        <h3 class="text-ocean mb-4"><i class="fas fa-comments me-2"></i>Commentaires récents</h3>
                        <div class="table-responsive">
                            <table id="commentairesTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Commentaire</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commentaires as $commentaire): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($commentaire['id']) ?></td>
                                        <td><?= htmlspecialchars($commentaire['nom']) ?></td>
                                        <td><?= htmlspecialchars($commentaire['prenom']) ?></td>
                                        <td><?= htmlspecialchars($commentaire['commentaire']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($commentaire['date_creation'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-edit edit-comment-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCommentModal"
                                                    data-id="<?= $commentaire['id'] ?>"
                                                    data-nom="<?= htmlspecialchars($commentaire['nom']) ?>"
                                                    data-prenom="<?= htmlspecialchars($commentaire['prenom']) ?>"
                                                    data-commentaire="<?= htmlspecialchars($commentaire['commentaire']) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-comment-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteCommentModal"
                                                    data-id="<?= $commentaire['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation des DataTables
            $('#donsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"
                },
                "order": [[6, "desc"]],
                "responsive": true
            });
            
            $('#commentairesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"
                },
                "order": [[4, "desc"]],
                "responsive": true
            });

            // Gestion de la suppression des dons
            $('.delete-don-btn').click(function() {
                var donId = $(this).data('id');
                var deleteUrl = 'admin_dashboard.php?delete_don=' + donId + '&tab=dons';
                $('#confirmDeleteDon').attr('href', deleteUrl);
            });

            // Gestion de la suppression des commentaires
            $('.delete-comment-btn').click(function() {
                var commentId = $(this).data('id');
                var deleteUrl = 'admin_dashboard.php?delete_comment=' + commentId + '&tab=commentaires';
                $('#confirmDeleteComment').attr('href', deleteUrl);
            });

            // Gestion de l'édition des dons
            $('.edit-don-btn').click(function() {
                $('#editDonId').val($(this).data('id'));
                $('#editMontant').val($(this).data('montant'));
                $('#editNom').val($(this).data('nom'));
                $('#editEmail').val($(this).data('email'));
                $('#editTelephone').val($(this).data('telephone'));
                
                // Correction pour le mode de paiement
                var modePaiement = $(this).data('mode-paiement');
                $('#editModePaiement option').each(function() {
                    if ($(this).val() === modePaiement) {
                        $(this).prop('selected', true);
                        return false; // Sortir de la boucle une fois trouvé
                    }
                });
                
                $('#editMessage').val($(this).data('message'));
            });

            // Gestion de l'édition des commentaires
            $('.edit-comment-btn').click(function() {
                $('#editCommentId').val($(this).data('id'));
                $('#editNomComment').val($(this).data('nom'));
                $('#editPrenomComment').val($(this).data('prenom'));
                $('#editCommentaire').val($(this).data('commentaire'));
            });

            // Réattacher les événements après le tri ou la pagination des DataTables
            $('#donsTable').on('draw.dt', function() {
                $('.delete-don-btn').click(function() {
                    var donId = $(this).data('id');
                    var deleteUrl = 'admin_dashboard.php?delete_don=' + donId + '&tab=dons';
                    $('#confirmDeleteDon').attr('href', deleteUrl);
                });
                
                $('.edit-don-btn').click(function() {
                    $('#editDonId').val($(this).data('id'));
                    $('#editMontant').val($(this).data('montant'));
                    $('#editNom').val($(this).data('nom'));
                    $('#editEmail').val($(this).data('email'));
                    $('#editTelephone').val($(this).data('telephone'));
                    
                    var modePaiement = $(this).data('mode-paiement');
                    $('#editModePaiement option').each(function() {
                        if ($(this).val() === modePaiement) {
                            $(this).prop('selected', true);
                            return false;
                        }
                    });
                    
                    $('#editMessage').val($(this).data('message'));
                });
            });

            $('#commentairesTable').on('draw.dt', function() {
                $('.delete-comment-btn').click(function() {
                    var commentId = $(this).data('id');
                    var deleteUrl = 'admin_dashboard.php?delete_comment=' + commentId + '&tab=commentaires';
                    $('#confirmDeleteComment').attr('href', deleteUrl);
                });
                
                $('.edit-comment-btn').click(function() {
                    $('#editCommentId').val($(this).data('id'));
                    $('#editNomComment').val($(this).data('nom'));
                    $('#editPrenomComment').val($(this).data('prenom'));
                    $('#editCommentaire').val($(this).data('commentaire'));
                });
            });
            
            // Si on a un don à éditer (passé en paramètre GET)
            <?php if ($don_to_edit): ?>
            $(window).on('load', function() {
                $('#editDonId').val('<?= $don_to_edit['id'] ?>');
                $('#editMontant').val('<?= $don_to_edit['montant'] ?>');
                $('#editNom').val('<?= htmlspecialchars($don_to_edit['nom']) ?>');
                $('#editEmail').val('<?= htmlspecialchars($don_to_edit['email']) ?>');
                $('#editTelephone').val('<?= htmlspecialchars($don_to_edit['telephone']) ?>');
                
                var modePaiement = '<?= htmlspecialchars($don_to_edit['mode_paiement']) ?>';
                $('#editModePaiement option').each(function() {
                    if ($(this).val() === modePaiement) {
                        $(this).prop('selected', true);
                        return false;
                    }
                });
                
                $('#editMessage').val('<?= htmlspecialchars($don_to_edit['message']) ?>');
                $('#editDonModal').modal('show');
            });
            <?php endif; ?>
            
            // Si on a un commentaire à éditer (passé en paramètre GET)
            <?php if ($comment_to_edit): ?>
            $(window).on('load', function() {
                $('#editCommentId').val('<?= $comment_to_edit['id'] ?>');
                $('#editNomComment').val('<?= htmlspecialchars($comment_to_edit['nom']) ?>');
                $('#editPrenomComment').val('<?= htmlspecialchars($comment_to_edit['prenom']) ?>');
                $('#editCommentaire').val('<?= htmlspecialchars($comment_to_edit['commentaire']) ?>');
                $('#editCommentModal').modal('show');
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>