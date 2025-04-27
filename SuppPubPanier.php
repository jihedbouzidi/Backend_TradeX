<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Gestion des requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'Panier.php';

// Initialiser la réponse
$response = [
    'status' => 'error',
    'message' => 'Action non effectuée'
];

try {
    // Récupération des données
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception("Aucune donnée reçue", 400);
    }

    $data = json_decode($input);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Données JSON invalides", 400);
    }

    // Validation des données
    $required = ['utilisateur_id', 'publication_id'];
    foreach ($required as $field) {
        if (!isset($data->$field)) {
            throw new Exception("Champ manquant: $field", 400);
        }
        if (!is_numeric($data->$field)) {
            throw new Exception("$field doit être numérique", 400);
        }
    }

    $panier = new Panier($db);
    $panier->utilisateur_id = $data->utilisateur_id;
    $panier->publication_id = $data->publication_id;

    // Vérifier d'abord si l'article existe dans le panier
    $checkStmt = $db->prepare("SELECT id FROM panier WHERE utilisateur_id = ? AND publication_id = ?");
    $checkStmt->execute([$panier->utilisateur_id, $panier->publication_id]);

    if ($checkStmt->rowCount() === 0) {
        $response = [
            'status' => 'success',
            'message' => 'Article non présent dans le panier'
        ];
    } else {
        // Suppression de l'article
        if ($panier->delete()) {
            $response = [
                'status' => 'success',
                'message' => 'Article supprimé du panier'
            ];
        } else {
            throw new Exception("Échec de la suppression", 500);
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

// Envoyer la réponse JSON
echo json_encode($response);
exit();
?>