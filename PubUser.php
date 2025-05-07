<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(["status" => "error", "message" => "ID de publication manquant"]);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Mettre à jour la publication principale
        $sql = "UPDATE publication SET 
                type_app = :type, 
                description = :description, 
                objectif = :objectif, 
                facebook = :facebook, 
                whatsapp = :whatsapp 
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':type' => $data['type'],
            ':description' => $data['description'],
            ':objectif' => $data['objectif'],
            ':facebook' => $data['facebook'] ?? null,
            ':whatsapp' => $data['whatsapp'] ?? null,
            ':id' => $data['id']
        ]);

        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Publication mise à jour avec succès",
            "data" => [
                "id" => $data['id'],
                "type_app" => $data['type'],
                "description" => $data['description'],
                "objectif" => $data['objectif'],
                "facebook" => $data['facebook'] ?? null,
                "whatsapp" => $data['whatsapp'] ?? null
            ]
        ]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["status" => "error", "message" => "Erreur de base de données: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Méthode non autorisée"]);
}
?>