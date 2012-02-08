<?php

class Message {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $body = "";
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
            $q = "SELECT * FROM " . DB_PREFIX . "messages WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->body = $ret->body;
            $this->created = $ret->created;
            $this->updated = $ret->updated;
        }
    }

    public function getId() {
        return $this->id;
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

    public function getBody() {
        return $this->body;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $body) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $body = mysql_real_escape_string($body);
        $q = "INSERT INTO " . DB_PREFIX . "messages (creator, project_id, body, created, updated) VALUES ($creator, $project_id, '$body', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // TODO Possibly some activity needs to be added
            return $uid;
        }
        return false;
    }

    public function update($message, $body) {
        $body = mysql_real_escape_string($body);
        $q = "UPDATE " . DB_PREFIX . "messages SET body='$body' WHERE id = {$message->id}";
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "messages WHERE id = {$this->id}";
        return query_delete($q);
    }
}
