<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Désactiver l'affichage des erreurs PHP dans la réponse
ini_set('display_errors', 0);
error_reporting(0);

// Fonction pour envoyer une réponse JSON
function sendJsonResponse($status, $message, $data = null) {
    http_response_code($status);
    echo json_encode([
        'status' => $status === 200 ? 'success' : 'error',
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Fonction pour logger les erreurs
function logError($message, $data = null) {
    error_log("TradeX Error: " . $message);
    if ($data) {
        error_log("TradeX Error Data: " . print_r($data, true));
    }
}

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Vérification des données requises
        $requiredFields = ['utilisateur_id', 'nomPre', 'email', 'telephone'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendJsonResponse(400, "Le champ $field est requis");
            }
        }

        $conn->beginTransaction();

        // Récupération des données
        $utilisateur_id = $_POST['utilisateur_id'];
        $nomPre = $_POST['nomPre'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $localisation = $_POST['localisation'] ?? '';
        $specialite = $_POST['specialite'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $whatsapp = $_POST['whatsapp'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $photo_profil_name = null;

        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            sendJsonResponse(400, "Format d'email invalide");
        }

        // Gestion de la photo de profil
        if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $detectedType = mime_content_type($_FILES['photo_profil']['tmp_name']);
            
            if (!in_array($detectedType, $allowedTypes)) {
                sendJsonResponse(400, 'Seuls les fichiers JPEG, PNG et GIF sont autorisés');
            }

            // Utiliser le nom original du fichier
            $fileName = $_FILES['photo_profil']['name'];
            
            // Sauvegarder le chemin relatif dans la base de données
            $photo_profil_name = 'public/' . $fileName;
        }

        // Gestion du mot de passe
        if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];

            $stmt = $conn->prepare("SELECT mot_de_passe FROM utilisateur WHERE id = ?");
            $stmt->execute([$utilisateur_id]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($old_password, $user['mot_de_passe'])) {
                sendJsonResponse(400, 'Ancien mot de passe incorrect');
            }

            if (strlen($new_password) < 8) {
                sendJsonResponse(400, 'Le mot de passe doit contenir au moins 8 caractères');
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $utilisateur_id]);
        }

        // Mise à jour des données utilisateur
        $sql = "UPDATE utilisateur SET 
                nomPre = :nomPre,
                email = :email,
                telephone = :telephone,
                localisation = :localisation,
                specialite = :specialite,
                facebook = :facebook,
                whatsapp = :whatsapp,
                instagram = :instagram";

        if ($photo_profil_name !== null) {
            $sql .= ", chemin_photo = :photo_profil";
        }

        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $params = [
            ':nomPre' => $nomPre,
            ':email' => $email,
            ':telephone' => $telephone,
            ':localisation' => $localisation,
            ':specialite' => $specialite,
            ':facebook' => $facebook,
            ':whatsapp' => $whatsapp,
            ':instagram' => $instagram,
            ':id' => $utilisateur_id
        ];

        if ($photo_profil_name !== null) {
            $params[':photo_profil'] = $photo_profil_name;
        }

        if (!$stmt->execute($params)) {
            throw new Exception("Erreur lors de la mise à jour du profil");
        }

        $conn->commit();

        // Récupération des données mises à jour
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$utilisateur_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            sendJsonResponse(404, "Utilisateur non trouvé");
        }

        unset($user['mot_de_passe']);
        sendJsonResponse(200, 'Profil mis à jour avec succès', $user);

    } catch (Exception $e) {
        $conn->rollBack();
        logError("Exception lors de la mise à jour du profil", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        sendJsonResponse(500, $e->getMessage());
    }
} else {
    sendJsonResponse(405, 'Méthode non autorisée');
}
?>