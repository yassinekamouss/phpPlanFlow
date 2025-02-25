<?php
namespace App\Models;

use DateTime;

class User {
    private ?int $id = null;
    private $name;
    private $email;
    private $password;
    private $role ;
    private $avatar;
    private $created_at;

    public function __construct($name, $email, $password, $role, $avatar) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->avatar = $avatar;
        $this->created_at = new DateTime();
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }
    
    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }

    public function getAvatar() { return $this->avatar; }  
    public function setAvatar($avatar) { $this->avatar = $avatar; } 

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getPassword() { return $this->password; }
    public function setPassword($password) { $this->password = $password; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    //toString
    public function __toString() {
        return "User [
            id: $this->id,
            name: $this->name,
            email: $this->email,
            password: $this->password, 
            role: $this->role,
            avatar: $this->avatar,
            created_at: $this->created_at
        ]";
    }
}