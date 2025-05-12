<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
require_once '../configs/config.php';
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

    // Supprimer les images associées
    $stmt = $conn->prepare("SELECT chemin FROM imagesPub WHERE publication_id = :id");
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as $image) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image['chemin'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $image['chemin']);
        }
    }

    // Supprimer les entrées dans la base de données
    $conn->beginTransaction();

    $stmt = $conn->prepare("DELETE FROM imagesPub WHERE publication_id = :id");
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM panier WHERE publication_id = :id");
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM publication WHERE id = :id");
    $stmt->bindParam(':id', $data['publication_id']);
    $stmt->execute();

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Publication supprimée avec succès']);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>