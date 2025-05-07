<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");

// Autoriser les requêtes OPTIONS pour le pré-vol CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';

try {
    if (!isset($_GET['utilisateur_id'])) {
        error_log("Missing utilisateur_id parameter");
        http_response_code(400);
        echo json_encode(["error" => "ID utilisateur manquant"]);
        exit();
    }

    $userId = (int)$_GET['utilisateur_id'];
    error_log("Fetching publications for user ID: " . $userId);
    
    // Vérifier la connexion à la base de données
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Requête principale avec tous les champs nécessaires
    $stmt = $conn->prepare("
        SELECT 
            p.id,
            p.type_app,
            p.description,
            p.objectif,
            p.facebook,
            p.whatsapp,
            p.date_publication,
            u.nomPre,
            u.chemin_photo
        FROM publication p
        JOIN utilisateur u ON p.utilisateur_id = u.id
        WHERE p.utilisateur_id = ?
        ORDER BY p.date_publication DESC
    ");
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->errorInfo()[2]);
    }
    
    $stmt->execute([$userId]);
    error_log("Main query executed successfully");

    $publications = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Récupération des images
        $imgStmt = $conn->prepare("SELECT chemin FROM imagesPub WHERE publication_id = ?");
        $imgStmt->execute([$row['id']]);
        $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Construction de l'objet publication selon la structure attendue par le composant React
        $publications[] = [
            "id" => (int)$row['id'],
            "type_app" => $row['type_app'],
            "description" => $row['description'],
            "objectif" => $row['objectif'],
            "facebook" => $row['facebook'],
            "whatsapp" => $row['whatsapp'],
            "date_publication" => $row['date_publication'],
            "user" => [
                "nomPre" => $row['nomPre'],
                "chemin_photo" => $row['chemin_photo']
            ],
            "images" => $images
        ];
    }
    
    error_log("Found " . count($publications) . " publications");
    error_log("Response data: " . json_encode($publications));
    
    http_response_code(200);
    echo json_encode($publications);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur de base de données",
        "message" => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "Erreur serveur",
        "message" => $e->getMessage()
    ]);
}
?>