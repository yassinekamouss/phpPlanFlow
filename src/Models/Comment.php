<?php
namespace App\Models;

use DateTime;

class Comment{
    private ?int $id = null;
    private int $task_id;
    private int $author_id;
    private string $text;
    private DateTime $created_at;
    
    public function __construct(int $task_id, int $author_id, string $text) {
        $this->task_id = $task_id;
        $this->author_id = $author_id;
        $this->text = $text;
        $this->created_at = new DateTime();
    }
    
    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTaskId(): int { return $this->task_id; }
    public function setTaskId(int $task_id): void { $this->task_id = $task_id; }
    
    public function getAuthorId(): int { return $this->author_id; }
    public function setAuthorId(int $author_id): void { $this->author_id = $author_id; }
    
    public function getText(): string { return $this->text; }
    public function setText(string $text): void { $this->text = $text; }
    
    public function getCreatedAt(): DateTime { return $this->created_at; }
    public function setCreatedAt(DateTime $created_at): void { $this->created_at = $created_at; }

    // toString
    public function __toString() {
        return "Comment [
            id: $this->id,
            task_id: $this->task_id,
            author_id: $this->author_id,
            text: $this->text,
            created_at: $this->created_at
        ]";
    }
}