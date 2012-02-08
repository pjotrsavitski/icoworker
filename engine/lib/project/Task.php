<?php

class Task {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $description = "";
    public $created = "";
    public $updated = "";
    
    public function __construct($id = NULL) {
        if ($id) {
            $this->id = $id;
        }
        $this->load($this->id);
    }

    public function load($id = NULL) {
        $ret = false;
        if ($id) {
            $this->id = $id;
            $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->title = $ret->title;
            $this->description = $ret->description;
            $this->created = $ret->created;
            $this->updated = $ret->updated;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCrator() {
        return $this->creator;
    }

    public function getCreatorObject() {
        return new User($this->creator);
    }

    public function getProjectId() {
        return $this->project_id;
    }

    public function getProjectObject() {
        return new Project($this->project_id);
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $title, $description) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $q = "INSERT INTO " . DB_PREFIX . "tasks (creator, project_id, title, description, created, updated) VALUES ($creator, $project_id, '$title', '$description', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // TODO Possibly some activity needs to be added
            return $uid;
        }
        return false;
    }

    public function update($task, $title, $description) {
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $q = "UPDATE " . DB_PREFIX . "tasks SET title='$title', description='$description' WHERE id = {$task->id}";
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "tasks WHERE id = {$this->id}";
        return query_delete($q);
    }
}
