<?php
// Activer le reporting d'erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Gestion des requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../configs/config.php';

$response = ['status' => 'error', 'message' => 'Erreur inconnue'];

try {
    // Récupération des données
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception("Aucune donnée reçue", 400);
    }

    $data = json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON invalide: " . json_last_error_msg(), 400);
    }

    // Validation des données
    if (!isset($data->utilisateur_id) || !is_numeric($data->utilisateur_id)) {
        throw new Exception("ID utilisateur invalide", 400);
    }

    if (!isset($data->publication_id) || !is_numeric($data->publication_id)) {
        throw new Exception("ID publication invalide", 400);
    }

    // Utilisation directe de la connexion $conn depuis config.php
    global $conn;

    // Vérifier si l'article existe déjà
    $check = $conn->prepare("SELECT id FROM panier WHERE utilisateur_id = ? AND publication_id = ?");
    $check->execute([$data->utilisateur_id, $data->publication_id]);

    if ($check->rowCount() > 0) {
        $response = [
            'status' => 'success',
            'message' => 'Article déjà dans le panier'
        ];
    } else {
        // Ajouter au panier
        $insert = $conn->prepare("INSERT INTO panier (utilisateur_id, publication_id, date_ajout) VALUES (?, ?, NOW())");
        if ($insert->execute([$data->utilisateur_id, $data->publication_id])) {
            $response = [
                'status' => 'success',
                'message' => 'Article ajouté au panier',
                'inserted_id' => $conn->lastInsertId()
            ];
        } else {
            $errorInfo = $insert->errorInfo();
            throw new Exception("Échec de l'insertion: " . ($errorInfo[2] ?? 'Erreur inconnue'), 500);
        }
    }

} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ];
    http_response_code($e->getCode() ?: 500);
}

// Envoyer la réponse
echo json_encode($response);
exit();
?>