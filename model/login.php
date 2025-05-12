<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include '../configs/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Données JSON invalides.']);
        exit;
    }

    $login = trim($data['email']); // Peut contenir email ou nom d'utilisateur
    $mot_de_passe = $data['password'];

    try {
        // Requête SQL pour récupérer l'utilisateur par email OU nomPre
        $sql = "SELECT * FROM utilisateur WHERE email = :login OR nomPre = :login";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['login' => $login]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe et si le mot de passe correspond
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Renvoyer les informations de l'utilisateur
            echo json_encode([
                'status' => 'success',
                'message' => 'Connexion réussie!',
                'user' => [
                    'id' => $user['id'],
                    'nomPre' => $user['nomPre'],
                    'email' => $user['email'],
                    'telephone' => $user['telephone'],
                    'localisation' => $user['localisation'],
                    'specialite' => $user['specialite'],
                    'facebook' => $user['facebook'],
                    'whatsapp' => $user['whatsapp'],
                    'instagram' => $user['instagram'],
                    'chemin_photo' => $user['chemin_photo'],
                ],
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Identifiants incorrects.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la connexion : ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>