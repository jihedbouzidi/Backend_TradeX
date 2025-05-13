<?php
class Image {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function deleteImages($image_ids, $publication_id) {
        $stmtSelect = $this->db->prepare("SELECT chemin FROM imagesPub WHERE id = ? AND publication_id = ?");
        $stmtDelete = $this->db->prepare("DELETE FROM imagesPub WHERE id = ? AND publication_id = ?");
        
        foreach ($image_ids as $image_id) {
            // Récupérer le chemin avant suppression
            $stmtSelect->execute([$image_id, $publication_id]);
            $image = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            
            if ($image && file_exists($_SERVER['DOCUMENT_ROOT'] . $image['chemin'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $image['chemin']);
            }
            
            // Supprimer de la base de données
            $stmtDelete->execute([$image_id, $publication_id]);
        }
    }

    public function addImages($publication_id, $images) {
        $stmt = $this->db->prepare("INSERT INTO imagesPub (chemin, publication_id) VALUES (?, ?)");
        foreach ($images as $image_path) {
            $stmt->execute([$image_path, $publication_id]);
        }
    }
}
?>