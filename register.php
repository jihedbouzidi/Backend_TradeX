<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

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

    $nomPre = $data['nomPre'];
    $email = $data['email'];
    $password = $data['password'];
    $telephone = $data['telephone'] ?? 'vide';
    $localisation = $data['localisation'] ?? 'vide';
    $specialite = $data['specialite'] ?? 'vide';
    $facebook = $data['facebook'] ?? 'https://facebook.com/';
    $whatsapp = $data['whatsapp'] ?? 'https://whatsapp.com/';
    $instagram = $data['instagram'] ?? 'https://www.instagram.com/';

    try {
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
            exit;
        }

        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Définir le chemin de l'image par défaut (chemin relatif à partir du dossier public de React)
        $defaultImagePath = "/inconn.png";

        // Insérer l'utilisateur avec l'image de profil par défaut
        $stmt = $conn->prepare("
            INSERT INTO utilisateur (
                nomPre, email, mot_de_passe, telephone, localisation, specialite, 
                facebook, whatsapp, instagram, chemin_photo
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $nomPre, $email, $hashed_password, $telephone, $localisation, $specialite, 
            $facebook, $whatsapp, $instagram, $defaultImagePath
        ]);
        
        // Récupérer l'ID de l'utilisateur nouvellement créé
        $userId = $conn->lastInsertId();
        
        // Récupérer les informations complètes de l'utilisateur
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$userId]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'message' => 'Inscription réussie !',
            'user' => [
                'id' => $newUser['id'],
                'nomPre' => $newUser['nomPre'],
                'email' => $newUser['email'],
                'telephone' => $newUser['telephone'],
                'localisation' => $newUser['localisation'],
                'specialite' => $newUser['specialite'],
                'facebook' => $newUser['facebook'],
                'whatsapp' => $newUser['whatsapp'],
                'instagram' => $newUser['instagram'],
                'chemin_photo' => $newUser['chemin_photo']
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>