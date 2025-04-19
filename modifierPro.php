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
    try {
        $conn->beginTransaction();

        // Récupération des données
        $utilisateur_id = $_POST['utilisateur_id'];
        $nomPre = $_POST['nomPre'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone'];
        $localisation = $_POST['localisation'];
        $specialite = $_POST['specialite'];
        $facebook = $_POST['facebook'];
        $whatsapp = $_POST['whatsapp'];
        $instagram = $_POST['instagram'];
        $photo_profil_name = null;

        // Gestion de la photo de profil
        if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
            // Récupérer seulement le nom du fichier
            $fileName = basename($_FILES['photo_profil']['name']);
            $photo_profil_name = $fileName; // Stocker juste le nom du fichier
            
            // Supprimer l'ancienne photo référence si elle existe
            $stmt = $conn->prepare("SELECT photo_profil FROM utilisateur WHERE id = ?");
            $stmt->execute([$utilisateur_id]);
            $oldPhoto = $stmt->fetchColumn();
        }

        // Gestion du mot de passe
        if (!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];

            $stmt = $conn->prepare("SELECT mot_de_passe FROM utilisateur WHERE id = ?");
            $stmt->execute([$utilisateur_id]);
            $user = $stmt->fetch();

            if (!password_verify($old_password, $user['mot_de_passe'])) {
                throw new Exception('Ancien mot de passe incorrect.');
            }

            if (strlen($new_password) < 8) {
                throw new Exception('Le mot de passe doit contenir au moins 8 caractères.');
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
            $sql .= ", photo_profil = :photo_profil";
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

        $stmt->execute($params);
        $conn->commit();

        // Récupération des données mises à jour
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute([$utilisateur_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        unset($user['mot_de_passe']);

        echo json_encode([
            'status' => 'success',
            'message' => 'Profil mis à jour avec succès',
            'user' => $user
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}
?>