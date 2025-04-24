<?php
// Activation du reporting d'erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
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
    error_log("[AffPublication] " . $message);
}

try {
    // Récupération des paramètres de requête
    $params = $_GET;
    
    // Construction de la requête SQL de base
    $sql = "SELECT p.*, 
            u.nomPre, 
            u.chemin_photo, 
            u.facebook AS user_facebook, 
            u.whatsapp AS user_whatsapp,
            u.instagram AS user_instagram
            FROM publication p
            LEFT JOIN utilisateur u ON p.utilisateur_id = u.id
            WHERE 1=1";

    // Filtre par terme de recherche
    if (!empty($params['search'])) {
        $searchTerm = $params['search'];
        $sql .= " AND (p.description LIKE :searchTerm OR u.nomPre LIKE :searchTerm)";
    }

    // Filtre par type d'appareil
    if (!empty($params['type']) && $params['type'] !== 'toutes') {
        $sql .= " AND p.type_app = :deviceType";
    }

    // Filtres spécifiques pour PC
    if (isset($params['type']) && $params['type'] === 'pc') {
        if (!empty($params['disqueDurType'])) {
            $sql .= " AND (p.description LIKE :disqueDurType)";
        }
        
        if (!empty($params['disqueDurCapacite'])) {
            $sql .= " AND (p.description LIKE :disqueDurCapacite)";
        }
        
        if (!empty($params['ram'])) {
            $sql .= " AND (p.description LIKE :ram)";
        }
        
        if (!empty($params['carteGraphique'])) {
            $sql .= " AND (p.description LIKE :carteGraphique)";
        }
        
        if (!empty($params['processeur'])) {
            $sql .= " AND (p.description LIKE :processeur)";
        }
    }

    // Filtres spécifiques pour Mobile
    if (isset($params['type']) && $params['type'] === 'mobile') {
        if (!empty($params['camera'])) {
            $sql .= " AND (p.description LIKE :camera)";
        }
        
        if (!empty($params['stockage'])) {
            $sql .= " AND (p.description LIKE :stockage)";
        }
        
        if (!empty($params['ram'])) {
            $sql .= " AND (p.description LIKE :ram)";
        }
        
        if (!empty($params['batterie'])) {
            $sql .= " AND (p.description LIKE :batterie)";
        }
    }

    // Tri par date de publication (du plus récent au plus ancien)
    $sql .= " ORDER BY p.date_publication DESC";

    // Préparation de la requête
    $stmt = $conn->prepare($sql);

    // Liaison des paramètres
    if (!empty($params['search'])) {
        $searchParam = "%" . $params['search'] . "%";
        $stmt->bindParam(':searchTerm', $searchParam);
    }

    if (!empty($params['type']) && $params['type'] !== 'toutes') {
        $stmt->bindParam(':deviceType', $params['type']);
    }

    // Liaison des paramètres PC
    if (isset($params['type']) && $params['type'] === 'pc') {
        if (!empty($params['disqueDurType'])) {
            $disqueDurParam = "%" . $params['disqueDurType'] . "%";
            $stmt->bindParam(':disqueDurType', $disqueDurParam);
        }
        
        if (!empty($params['disqueDurCapacite'])) {
            $capaciteParam = "%" . $params['disqueDurCapacite'] . "%";
            $stmt->bindParam(':disqueDurCapacite', $capaciteParam);
        }
        
        if (!empty($params['ram'])) {
            $ramParam = "%" . $params['ram'] . "%";
            $stmt->bindParam(':ram', $ramParam);
        }
        
        if (!empty($params['carteGraphique'])) {
            $carteGraphiqueParam = "%" . $params['carteGraphique'] . "%";
            $stmt->bindParam(':carteGraphique', $carteGraphiqueParam);
        }
        
        if (!empty($params['processeur'])) {
            $processeurParam = "%" . $params['processeur'] . "%";
            $stmt->bindParam(':processeur', $processeurParam);
        }
    }

    // Liaison des paramètres Mobile
    if (isset($params['type']) && $params['type'] === 'mobile') {
        if (!empty($params['camera'])) {
            $cameraParam = "%" . $params['camera'] . "%";
            $stmt->bindParam(':camera', $cameraParam);
        }
        
        if (!empty($params['stockage'])) {
            $stockageParam = "%" . $params['stockage'] . "%";
            $stmt->bindParam(':stockage', $stockageParam);
        }
        
        if (!empty($params['ram'])) {
            $ramParam = "%" . $params['ram'] . "%";
            $stmt->bindParam(':ram', $ramParam);
        }
        
        if (!empty($params['batterie'])) {
            $batterieParam = "%" . $params['batterie'] . "%";
            $stmt->bindParam(':batterie', $batterieParam);
        }
    }

    // Exécution de la requête
    $stmt->execute();

    $publications = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Récupération des images
        $images = array();
        $imagesSql = "SELECT chemin FROM imagesPub WHERE publication_id = :pubId";
        $imagesStmt = $conn->prepare($imagesSql);
        $imagesStmt->bindParam(':pubId', $row['id']);
        $imagesStmt->execute();
        
        while($imageRow = $imagesStmt->fetch(PDO::FETCH_ASSOC)) {
            $images[] = $imageRow['chemin'];
        }
        
        // Formatage des données de publication
        $publication = array(
            "id" => $row['id'],
            "user" => array(
                "id" => $row['utilisateur_id'],
                "nom" => $row['nomPre'],
                "photoProURL" => $row['chemin_photo'],
                "LinkProfile" => "#/profile/" . $row['utilisateur_id'],
                "facebook" => $row['user_facebook'],
                "whatsapp" => $row['user_whatsapp'],
                "instagram" => $row['user_instagram']
            ),
            "description" => $row['description'],
            "images" => $images,
            "type" => $row['type_app'],
            "date_pub" => $row['date_publication'],
            "facebookLink" => $row['facebook'],
            "whatsappLink" => $row['whatsapp']
        );
        
        $publications[] = $publication;
    }

    // Réponse JSON
    echo json_encode(array(
        "status" => "success",
        "publications" => $publications
    ));

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    log_error($e->getMessage());
}
?>