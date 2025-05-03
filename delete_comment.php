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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_dashboard.php?tab=commentaires');
    exit;
}

$comment_id = (int)$_GET['id'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aya2";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si le commentaire existe
    $check_stmt = $conn->prepare("SELECT id FROM commentaires WHERE id = ?");
    $check_stmt->execute([$comment_id]);
    
    if ($check_stmt->rowCount() === 0) {
        header('Location: admin_dashboard.php?tab=commentaires');
        exit;
    }
    
    // Supprimer le commentaire
    $stmt = $conn->prepare("DELETE FROM commentaires WHERE id = ?");
    $stmt->execute([$comment_id]);
    
    // Rediriger vers l'onglet commentaires
    header('Location: admin_dashboard.php?tab=commentaires');
    exit;
    
} catch(PDOException $e) {
    error_log("Erreur de base de données : " . $e->getMessage());
    header('Location: admin_dashboard.php?tab=commentaires&error=1');
    exit;
}