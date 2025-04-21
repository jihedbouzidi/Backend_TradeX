<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Données JSON invalides.']);
        exit;
    }

    try {
        // Vérifier si l'utilisateur est connecté
        if (!isset($data['utilisateur_id'])) {
            throw new Exception('Utilisateur non connecté.');
        }

        // Démarrer une transaction
        $conn->beginTransaction();

        // Insérer la publication
        $sql = "INSERT INTO publication (utilisateur_id, type, titre, description, facebook, whatsapp) 
                VALUES (:utilisateur_id, :type, :titre, :description, :facebook, :whatsapp)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'utilisateur_id' => $data['utilisateur_id'],
            'type' => $data['type'] ?? 'default',
            'titre' => $data['titre'] ?? 'Publication sans titre',
            'description' => $data['description'],
            'facebook' => $data['facebookLink'],
            'whatsapp' => $data['whatsappLink']
        ]);

        $publication_id = $conn->lastInsertId();

        // Insérer les images associées
        if (isset($data['images']) && is_array($data['images'])) {
            $sql = "INSERT INTO imagesPub (chemin, publication_id) VALUES (:chemin, :publication_id)";
            $stmt = $conn->prepare($sql);

            foreach ($data['images'] as $image) {
                // Extraire uniquement le nom du fichier du chemin complet
                $imageName = basename($image);
                $stmt->execute([
                    'chemin' => "/" . $imageName,
                    'publication_id' => $publication_id
                ]);
            }
        }

        // Insérer dans VotrePub
        $sql = "INSERT INTO VotrePub (utilisateur_id, publication_id) VALUES (:utilisateur_id, :publication_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'utilisateur_id' => $data['utilisateur_id'],
            'publication_id' => $publication_id
        ]);

        // Valider la transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Publication ajoutée avec succès.',
            'publication_id' => $publication_id
        ]);

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de l\'ajout de la publication: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>