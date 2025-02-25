<?php
namespace App\Models;

use DateTime;

class Task{
    private ?int $id = null;
    private $name;
    private $description;
    private $created_at;
    private $start_date;
    private $end_date;
    private $status ;
    private $priority ;
    private $assignee_id;
    private $project_id;

    public function __construct($name, $description, $assignee_id , $start_date, $end_date , $status , $priority , $project_id) {
        $this->name = $name;
        $this->description = $description;
        $this->assignee_id = $assignee_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->priority = $priority;
        $this->project_id = $project_id;
        $this->created_at = new DateTime();
    }

    // Getters and Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getDescription() { return $this->description; }
    public function setDescription($description){ $this->description = $description; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getStartDate() { return $this->start_date; }
    public function setStartDate($start_date) { $this->start_date = $start_date; }

    public function getEndDate() { return $this->end_date; }
    public function setEndDate($end_date) { $this->end_date = $end_date; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }

    public function getPriority() { return $this->priority; }
    public function setPriority($priority) { $this->priority = $priority; }

    public function getAssigneeId() { return $this->assignee_id; }
    public function setAssigneeId($assignee_id) { $this->assignee_id = $assignee_id; }

    public function getProjectId() { return $this->project_id; }
    public function setProjectId($project_id) { $this->project_id = $project_id; }

    //toString
    public function __toString() {
        return "Task [
            id: $this->id,
            name: $this->name,
            description: $this->description
            created_at: $this->created_at,
            start_date: $this->start_date,
            end_date: $this->end_date,
            status: $this->status,
            priority: $this->priority,
            assignee_id: $this->assignee_id,
            project_id: $this->project_id
        ]";
    }
}
