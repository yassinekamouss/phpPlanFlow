<?php
namespace App\Models;

use DateTime;

class Notification{
    private ?int $id = null ;
    private int $user_id;
    private int $task_id ;
    private string $text;
    private bool $read_status;
    private DateTime $created_at;

    public function __construct(int $user_id, int $task_id, string $text, bool $read_status) {
        $this->user_id = $user_id;
        $this->task_id = $task_id;
        $this->text = $text;
        $this->read_status = $read_status;
        $this->created_at = new DateTime();
    }
    // Getters and Setters 
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id) : void { $this->id = $id;}

    public function getUserId(): int { return $this->user_id;}
    public function setUserId(int $user_id) : void { $this->user_id = $user_id;}

    public function getTaskId(): int { return $this->task_id;}
    public function setTaskId(int $task_id) : void { $this->task_id = $task_id;}

    public function getText(): string { return $this->text;}
    public function setText(string $text) : void { $this->text = $text;}

    public function getReadStatus(): bool { return $this->read_status;}
    public function setReadStatus(bool $read_status) : void { $this->read_status = $read_status;}

    // toString 
    public function __toString() {
        return "Notification [
            id: $this->id,
            user_id: $this->user_id,
            task_id: $this->task_id,
            text: $this->text,
            read_status: $this->read_status,
            created_at: $this->created_at
        ]";
    }
}
?>