<?php
// Activation du reporting d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");

// Gestion des requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclusion de la configuration de la base de données
include 'config.php';

// Fonction pour logger les erreurs
function log_error($message) {
    error_log("[AddPublication] " . $message);
}

try {
    // Récupération des données JSON de la requête
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validation des données requises
    if (!isset($data['utilisateur_id']) || !isset($data['type']) || !isset($data['description'])|| !isset($data['objectif'])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Données manquantes: utilisateur_id, type , description ou objectif requis"
        ]);
        exit();
    }

    // Préparation des données
    $utilisateur_id = $data['utilisateur_id'];
    $type_app = $data['type'];
    $description = $data['description'];
    $objectif = $data['objectif'];
    $facebook = isset($data['facebookLink']) ? $data['facebookLink'] : null;
    $whatsapp = isset($data['whatsappLink']) ? $data['whatsappLink'] : null;
    $images = isset($data['images']) ? $data['images'] : [];

    // Validation du type d'appareil
    $allowed_types = ['pc', 'mobile'];
    if (!in_array(strtolower($type_app), $allowed_types)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Type d'appareil invalide. Doit être 'pc' ou 'mobile'"
        ]);
        exit();
    }

    // Insertion de la publication dans la base de données
    $sql = "INSERT INTO publication (utilisateur_id, type_app, description, objectif ,facebook, whatsapp, date_publication) 
            VALUES (:utilisateur_id, :type_app, :description, :objectif, :facebook, :whatsapp, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->bindParam(':type_app', $type_app);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':objectif', $objectif);
    $stmt->bindParam(':facebook', $facebook);
    $stmt->bindParam(':whatsapp', $whatsapp);

    if (!$stmt->execute()) {
        throw new PDOException("Erreur lors de l'insertion de la publication");
    }

    // Récupération de l'ID de la publication nouvellement créée
    $publication_id = $conn->lastInsertId();

    // Insertion des images dans la table imagesPub
    if (!empty($images)) {
        $imagesSql = "INSERT INTO imagesPub (chemin, publication_id) VALUES (:chemin, :publication_id)";
        $imagesStmt = $conn->prepare($imagesSql);

        foreach ($images as $image) {
            // Nettoyage du chemin de l'image (on garde seulement le nom du fichier avec extension)
            $cleanPath = '/' . basename($image);
            
            $imagesStmt->bindParam(':chemin', $cleanPath);
            $imagesStmt->bindParam(':publication_id', $publication_id);
            $imagesStmt->execute();
        }
    }
    // Réponse JSON
    echo json_encode([
        "status" => "success",
        "message" => "Publication ajoutée avec succès",
        "publication_id" => $publication_id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erreur de base de données: " . $e->getMessage()
    ]);
    log_error($e->getMessage());
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Erreur: " . $e->getMessage()
    ]);
    log_error($e->getMessage());
}
?>