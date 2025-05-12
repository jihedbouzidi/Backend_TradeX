<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../configs/config.php';

$utilisateur_id = $_GET['utilisateur_id'] ?? null;

if (!$utilisateur_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID utilisateur manquant']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT p.*, GROUP_CONCAT(i.id, ':', i.chemin) as images_data 
        FROM publication p 
        LEFT JOIN imagesPub i ON p.id = i.publication_id 
        WHERE p.utilisateur_id = :utilisateur_id 
        GROUP BY p.id 
        ORDER BY p.date_publication DESC
    ");
    $stmt->bindParam(':utilisateur_id', $utilisateur_id);
    $stmt->execute();
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formater les images
    foreach ($publications as &$pub) {
        $pub['images'] = [];
        if (!empty($pub['images_data'])) {
            $imagePairs = explode(',', $pub['images_data']);
            foreach ($imagePairs as $pair) {
                if (strpos($pair, ':') !== false) {
                    list($id, $path) = explode(':', $pair, 2);
                    $pub['images'][] = ['id' => $id, 'chemin' => $path];
                }
            }
        }
        unset($pub['images_data']);
    }

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $publications]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>