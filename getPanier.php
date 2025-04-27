<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Réponse immédiate pour les requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php'; // Utilise  fichier config.php 
require_once 'Panier.php';

try {
    // Utilisation directe de la connexion $conn depuis config.php
    global $conn;
    
    if (!$conn) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    $panier = new Panier($conn);

    // Validation de l'ID utilisateur
    if (!isset($_GET['utilisateur_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "ID utilisateur manquant"]);
        exit();
    }

    $utilisateur_id = filter_var($_GET['utilisateur_id'], FILTER_VALIDATE_INT);
    
    if ($utilisateur_id === false || $utilisateur_id <= 0) {
        http_response_code(400);
        echo json_encode(["message" => "ID utilisateur invalide"]);
        exit();
    }

    // Récupération du panier
    $stmt = $panier->readByUser($utilisateur_id);
    
    if (!$stmt) {
        throw new Exception("Erreur lors de la récupération du panier");
    }

    $panier_arr = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Nettoyage et formatage des données
        $pub_item = array(
            "id" => (int)$row['id'],
            "description" => htmlspecialchars_decode($row['description']),
            "objectif" => htmlspecialchars_decode($row['objectif']),
            "type_app" => htmlspecialchars($row['type_app']),
            "date_publication" => $row['date_publication'],
            "nom" => htmlspecialchars($row['nomPre']),
            "photoProURL" => filter_var($row['chemin_photo'], FILTER_SANITIZE_URL),
            "images" => array(),
            "facebook" => filter_var($row['facebook'], FILTER_SANITIZE_URL),
            "whatsapp" => filter_var($row['whatsapp'], FILTER_SANITIZE_URL),
        );
        
        // Ajout des images si elles existent
        if (!empty($row['chemin_image'])) {
            $pub_item["images"][] = array(
                "chemin" => filter_var($row['chemin_image'], FILTER_SANITIZE_URL)
            );
        }
        
        array_push($panier_arr, $pub_item);
    }
    
    // Réponse JSON
    http_response_code(200);
    echo json_encode($panier_arr);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Erreur de base de données: " . $e->getMessage(),
        "error" => true
    ]);
    error_log("PDOException: " . $e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Erreur serveur: " . $e->getMessage(),
        "error" => true
    ]);
    error_log("Exception: " . $e->getMessage());
}
?>