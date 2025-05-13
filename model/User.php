<?php
class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance();
    }

    public function register($data) {
        $sql = "INSERT INTO utilisateur (
            nomPre, email, mot_de_passe, telephone, localisation, specialite, 
            facebook, whatsapp, instagram, chemin_photo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $data['nomPre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['telephone'] ?? 'vide',
            $data['localisation'] ?? 'vide',
            $data['specialite'] ?? 'vide',
            $data['facebook'] ?? 'https://facebook.com/',
            $data['whatsapp'] ?? 'https://whatsapp.com/',
            $data['instagram'] ?? 'https://www.instagram.com/',
            '/inconn.png'
        ]);

        return $this->conn->lastInsertId();
    }

    public function login($login, $password) {
        $sql = "SELECT * FROM utilisateur WHERE email = ? OR nomPre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            unset($user['mot_de_passe']);
            return $user;
        }
        return false;
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE utilisateur SET 
            nomPre = :nomPre,
            email = :email,
            telephone = :telephone,
            localisation = :localisation,
            specialite = :specialite,
            facebook = :facebook,
            whatsapp = :whatsapp,
            instagram = :instagram";

        if (isset($data['photo_profil'])) {
            $sql .= ", chemin_photo = :photo_profil";
        }

        if (!empty($data['new_password'])) {
            $sql .= ", mot_de_passe = :password";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        
        $params = [
            ':nomPre' => $data['nomPre'],
            ':email' => $data['email'],
            ':telephone' => $data['telephone'],
            ':localisation' => $data['localisation'],
            ':specialite' => $data['specialite'],
            ':facebook' => $data['facebook'],
            ':whatsapp' => $data['whatsapp'],
            ':instagram' => $data['instagram'],
            ':id' => $userId
        ];

        if (isset($data['photo_profil'])) {
            $params[':photo_profil'] = $data['photo_profil'];
        }

        if (!empty($data['new_password'])) {
            $params[':password'] = password_hash($data['new_password'], PASSWORD_BCRYPT);
        }

        return $stmt->execute($params);
    }

    public function getUser($userId) {
        $sql = "SELECT * FROM utilisateur WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            unset($user['mot_de_passe']);
        }
        
        return $user;
    }
}
?>