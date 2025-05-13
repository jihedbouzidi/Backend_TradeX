<?php
class Publication {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO publication 
            (utilisateur_id, type_app, description, objectif, facebook, whatsapp, date_publication) 
            VALUES (:utilisateur_id, :type_app, :description, :objectif, :facebook, :whatsapp, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':utilisateur_id' => $data['utilisateur_id'],
            ':type_app' => $data['type'],
            ':description' => $data['description'],
            ':objectif' => $data['objectif'],
            ':facebook' => $data['facebookLink'] ?? null,
            ':whatsapp' => $data['whatsappLink'] ?? null
        ]);

        $publicationId = $this->conn->lastInsertId();

        if (!empty($data['images'])) {
            $this->addImages($publicationId, $data['images']);
        }

        return $publicationId;
    }

    private function addImages($publicationId, $images) {
        $sql = "INSERT INTO imagesPub (chemin, publication_id) VALUES (:chemin, :publication_id)";
        $stmt = $this->conn->prepare($sql);

        foreach ($images as $image) {
            $cleanPath = '/' . basename($image);
            $stmt->execute([':chemin' => $cleanPath, ':publication_id' => $publicationId]);
        }
    }

    public function getAll($filters = []) {
        $sql = "SELECT p.*, u.nomPre, u.chemin_photo, u.facebook AS user_facebook, 
                u.whatsapp AS user_whatsapp, u.instagram AS user_instagram
                FROM publication p
                LEFT JOIN utilisateur u ON p.utilisateur_id = u.id
                WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (p.description LIKE :search OR u.nomPre LIKE :search)";
        }

        if (!empty($filters['objectif'])) {
            $sql .= " AND p.objectif LIKE :objectif";
        }

        if (!empty($filters['type']) && $filters['type'] !== 'toutes') {
            $sql .= " AND p.type_app = :type";
        }

        $sql .= " ORDER BY p.date_publication DESC";

        $stmt = $this->conn->prepare($sql);

        if (!empty($filters['search'])) {
            $stmt->bindValue(':search', "%{$filters['search']}%");
        }

        if (!empty($filters['objectif'])) {
            $stmt->bindValue(':objectif', "%{$filters['objectif']}%");
        }

        if (!empty($filters['type']) && $filters['type'] !== 'toutes') {
            $stmt->bindValue(':type', $filters['type']);
        }

        $stmt->execute();
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($publications as &$pub) {
            $pub['images'] = $this->getImages($pub['id']);
        }

        return $publications;
    }

    private function getImages($publicationId) {
        $sql = "SELECT chemin FROM imagesPub WHERE publication_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$publicationId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getUserPublications($userId) {
        $sql = "SELECT p.*, GROUP_CONCAT(i.id, ':', i.chemin) as images_data 
                FROM publication p 
                LEFT JOIN imagesPub i ON p.id = i.publication_id 
                WHERE p.utilisateur_id = ? 
                GROUP BY p.id 
                ORDER BY p.date_publication DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        return $publications;
    }

    public function update($publicationId, $userId, $data) {
        // Vérifier la propriété
        $sql = "SELECT utilisateur_id FROM publication WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$publicationId]);
        $pub = $stmt->fetch();

        if (!$pub || $pub['utilisateur_id'] != $userId) {
            return false;
        }

        // Mettre à jour la publication
        $sql = "UPDATE publication SET 
                type_app = :type_app, 
                description = :description, 
                objectif = :objectif, 
                facebook = :facebook, 
                whatsapp = :whatsapp 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':type_app' => $data['type_app'],
            ':description' => $data['description'],
            ':objectif' => $data['objectif'],
            ':facebook' => $data['facebook'],
            ':whatsapp' => $data['whatsapp'],
            ':id' => $publicationId
        ]);

        // Gérer les images
        if (!empty($data['images_to_delete'])) {
            $this->deleteImages($publicationId, $data['images_to_delete']);
        }

        if (!empty($data['new_images'])) {
            $this->addImages($publicationId, $data['new_images']);
        }

        return true;
    }

    private function deleteImages($publicationId, $imageIds) {
        // Récupérer les chemins avant suppression
        $sql = "SELECT chemin FROM imagesPub WHERE id = ? AND publication_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        // Supprimer les fichiers
        foreach ($imageIds as $id) {
            $stmt->execute([$id, $publicationId]);
            $image = $stmt->fetch();
            
            if ($image && file_exists($_SERVER['DOCUMENT_ROOT'] . $image['chemin'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $image['chemin']);
            }
        }

        // Supprimer de la base
        $sql = "DELETE FROM imagesPub WHERE id IN (" . implode(',', array_fill(0, count($imageIds), '?')) . ")";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($imageIds);
    }

    public function delete($publicationId, $userId) {
        // Vérifier la propriété
        $sql = "SELECT utilisateur_id FROM publication WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$publicationId]);
        $pub = $stmt->fetch();

        if (!$pub || $pub['utilisateur_id'] != $userId) {
            return false;
        }

        // Supprimer les images
        $images = $this->getImages($publicationId);
        foreach ($images as $image) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $image);
            }
        }

        // Supprimer de la base
        $this->conn->beginTransaction();

        try {
            $sql = "DELETE FROM imagesPub WHERE publication_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$publicationId]);

            $sql = "DELETE FROM panier WHERE publication_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$publicationId]);

            $sql = "DELETE FROM publication WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$publicationId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>