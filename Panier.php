<?php
class Panier {
    private $conn;
    private $table_name = "panier";
    public $utilisateur_id;
    public $publication_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($utilisateur_id, $publication_id) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (utilisateur_id, publication_id, date_ajout) 
                 VALUES (?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$utilisateur_id, $publication_id])) {
            return $this->conn->lastInsertId();
        }
        
        $errorInfo = $stmt->errorInfo();
        throw new Exception("Erreur SQL: " . ($errorInfo[2] ?? 'Unknown error'));
    }
    public function delete() {
        $query = "DELETE FROM panier 
                 WHERE utilisateur_id = :utilisateur_id 
                 AND publication_id = :publication_id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->utilisateur_id = htmlspecialchars(strip_tags($this->utilisateur_id));
        $this->publication_id = htmlspecialchars(strip_tags($this->publication_id));
        
        $stmt->bindParam(":utilisateur_id", $this->utilisateur_id, PDO::PARAM_INT);
        $stmt->bindParam(":publication_id", $this->publication_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        
        throw new Exception("Erreur lors de l'exécution de la requête");
    }
    
    public function readByUser($utilisateur_id) {
        $query = "SELECT p.*, u.nomPre, u.chemin_photo, i.chemin as chemin_image 
                  FROM panier pa
                  JOIN publication p ON pa.publication_id = p.id
                  JOIN utilisateur u ON p.utilisateur_id = u.id
                  LEFT JOIN imagesPub i ON p.id = i.publication_id
                  WHERE pa.utilisateur_id = :utilisateur_id
                  GROUP BY p.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":utilisateur_id", $utilisateur_id, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'exécution de la requête");
        }
        
        return $stmt;
    }
}
?>


