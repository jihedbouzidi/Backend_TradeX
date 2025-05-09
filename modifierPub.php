<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['publication_id']) || !isset($data['utilisateur_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Données manquantes']);
    exit;
}

try {
    // Vérifier que l'utilisateur est bien le propriétaire de la publication
    $stmt = $conn->prepare("SELECT utilisateur_id FROM publication WHERE id = :id");
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();
    $publication = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$publication || $publication['utilisateur_id'] != $data['utilisateur_id']) {
        echo json_encode(['status' => 'error', 'message' => 'Action non autorisée']);
        exit;
    }

    // Mettre à jour la publication
    $stmt = $conn->prepare("UPDATE publication SET 
        type_app = :type_app, 
        description = :description, 
        objectif = :objectif, 
        facebook = :facebook, 
        whatsapp = :whatsapp 
        WHERE id = :id");

    $stmt->bindParam(':type_app', $data['type_app']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':objectif', $data['objectif']);
    $stmt->bindParam(':facebook', $data['facebook']);
    $stmt->bindParam(':whatsapp', $data['whatsapp']);
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();

    // Gérer les images
    if (!empty($data['images_to_delete'])) {
        $stmt = $conn->prepare("DELETE FROM imagesPub WHERE id = :id AND publication_id = :pub_id");
        foreach ($data['images_to_delete'] as $image_id) {
            $stmt->bindParam(':id', $image_id);
            $stmt->bindParam(':pub_id', $data['publication_id']);
            $stmt->execute();
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Publication mise à jour avec succès']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>