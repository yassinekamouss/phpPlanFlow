<?php
namespace App\Models;

use DateTime;

class Project {
    private ?int $id = null;
    private $name;
    private $created_at;
    private $description;
    private $status;
    private $start_date;
    private $end_date;
    private int $project_manager_id;
    private array $tasks;

    public function __construct($name, $description, $status, $start_date, $end_date, $project_manager_id) {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->project_manager_id = $project_manager_id;
        $this->tasks = [];
        $this->created_at = new DateTime();
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function getStartDate() { return $this->start_date; }
    public function setStartDate($start_date) { $this->start_date = $start_date; }

    public function getEndDate() { return $this->end_date; }
    public function setEndDate($end_date) { $this->end_date = $end_date; }

    public function getProjectManagerId() { return $this->project_manager_id; }
    public function setProjectManagerId($project_manager_id) { $this->project_manager_id = $project_manager_id; }

    public function getTasks() { return $this->tasks; }
    public function addTask($task) { $this->tasks[] = $task; }

    //toString
    public function __toString() {
        return "Project [
            id: $this->id,
            name: $this->name,
            created_at: $this->created_at,
            description: $this->description,
            status: $this->status,
            start_date: $this->start_date,
            end_date: $this->end_date,
            project_manager_id: $this->project_manager_id,
        ]";
    }
}