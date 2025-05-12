<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../configs/config.php';
require_once '../model/Panier.php';

ini_set('display_errors', 0);
error_reporting(0);

$response = [
    'status' => 'error',
    'message' => 'Action non effectuée'
];

try {
    // Récupérer les données JSON du corps de la requête
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    if (!$data || !isset($data->utilisateur_id) || !isset($data->publication_id)) {
        throw new Exception("Données invalides", 400);
    }

    $panier = new Panier($conn); // Utiliser $conn au lieu de $db
    $panier->utilisateur_id = (int)$data->utilisateur_id;
    $panier->publication_id = (int)$data->publication_id;

    // Suppression directe
    if ($panier->delete()) {
        $response = [
            'status' => 'success',
            'message' => 'Article supprimé du panier'
        ];
    } else {
        throw new Exception("La suppression a échoué", 500);
    }

} catch (PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Erreur de base de données: ' . $e->getMessage(),
        'code' => 500
    ];
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ];
}

http_response_code($response['code'] ?? 200);
echo json_encode($response);
exit();
?>