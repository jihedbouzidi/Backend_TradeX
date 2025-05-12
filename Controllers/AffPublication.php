<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../configs/config.php';

function log_error($message) {
    error_log("[AffPublication] " . $message);
}

try {
    $params = $_GET;
    
    $sql = "SELECT p.*, 
            u.nomPre, 
            u.chemin_photo, 
            u.facebook AS user_facebook, 
            u.whatsapp AS user_whatsapp,
            u.instagram AS user_instagram
            FROM publication p
            LEFT JOIN utilisateur u ON p.utilisateur_id = u.id
            WHERE 1=1";

    if (!empty($params['search'])) {
        $searchTerm = "%" . $params['search'] . "%";
        $sql .= " AND (p.description LIKE :searchTerm OR u.nomPre LIKE :searchTerm)";
    }

    if (!empty($params['objectif'])) {
        $objectif = "%" . $params['objectif'] . "%";
        $sql .= " AND p.objectif LIKE :objectif";
    }

    if (!empty($params['type']) && $params['type'] !== 'toutes') {
        $sql .= " AND p.type_app = :deviceType";
    }

    $sql .= " ORDER BY p.date_publication DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params['search'])) {
        $stmt->bindParam(':searchTerm', $searchTerm);
    }

    if (!empty($params['objectif'])) {
        $stmt->bindParam(':objectif', $objectif);
    }

    if (!empty($params['type']) && $params['type'] !== 'toutes') {
        $stmt->bindParam(':deviceType', $params['type']);
    }

    $stmt->execute();

    $publications = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $images = array();
        $imagesSql = "SELECT chemin FROM imagesPub WHERE publication_id = :pubId";
        $imagesStmt = $conn->prepare($imagesSql);
        $imagesStmt->bindParam(':pubId', $row['id']);
        $imagesStmt->execute();
        
        while($imageRow = $imagesStmt->fetch(PDO::FETCH_ASSOC)) {
            $images[] = $imageRow['chemin'];
        }
        
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
            "objectif" => $row['objectif'],
            "images" => $images,
            "type" => $row['type_app'], // Renommé en 'type' pour correspondre au frontend
            "date_pub" => $row['date_publication'],
            "facebookLink" => $row['facebook'],
            "whatsappLink" => $row['whatsapp']
        );
        
        $publications[] = $publication;
    }

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