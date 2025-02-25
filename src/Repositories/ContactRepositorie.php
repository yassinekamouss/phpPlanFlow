<?php
namespace App\Repositories;

use Config\Database;
use App\Models\Contact;
use PDO;

class ContactRepositorie{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function send(Contact $contact){
        try{
            // Insérer les données dans la bdd
            $sql = "INSERT INTO contact (name, email, subject, message, status) 
                    VALUES (?, ?, ?, ?, 'nouveau')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $contact->getName(),
                $contact->getEmail(), 
                $contact->getSubject(),
                $contact->getMessage()
            ]);
            return true;

        }catch(\PDOException $e){
            throw new \Exception("Error sending message: " . $e->getMessage());
        }
    }

    public function findById($id){
        try{
            $sql = "SELECT * FROM contact WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$result){
                throw new \Exception("Message not found");
            }
            // Créer un objet Contact
            $contact = new Contact($result['name'], $result['email'], $result['subject'], $result['message']);
            $contact->setId($result['id']);
            $contact->setStatus($result['status']);

            return $contact;
        }catch(\PDOException $e){
            throw new \Exception("Error fetching message: " . $e->getMessage());
        }
    }

    public function findAll(){
        try{
            $sql = "SELECT * FROM contact ORDER BY status ASC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \Exception("Error fetching messages: " . $e->getMessage());
        }
    }

    public function update(Contact $contact){
        try{
            // Insérer les données dans la bdd
            $sql = "UPDATE contact SET name = ?, email = ?, subject = ?, message = ?, status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $contact->getName(),
                $contact->getEmail(), 
                $contact->getSubject(),
                $contact->getMessage(),
                $contact->getStatus(),
                $contact->getId()
            ]);
            return true;

        }catch(\PDOException $e){
            throw new \Exception("Error sending message: " . $e->getMessage());
        }
    }
}