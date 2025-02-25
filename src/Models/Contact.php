<?php
namespace App\Models;

use DateTime;

class Contact {
    private ?int $id = null;
    private string $name;
    private string $email;
    private string $subject;
    private string $message;
    private string $status = 'nouveau';
    private DateTime $created_at;

    public function __construct(string $name, string $email, string $subject, string $message, string $status = 'nouveau') {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->created_at = new DateTime();
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getSubject(): string { return $this->subject; }
    public function setSubject(string $subject): void { $this->subject = $subject; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): void { $this->status = $status; }

    public function getCreatedAt(): DateTime { return $this->created_at; }
    public function setCreatedAt(DateTime $created_at): void { $this->created_at = $created_at; }
}