<?php

class Activity {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $activity_type = "";// Either "activity" or "message"
    // XXX This should probably just be some activity type information
    public $activity_subtype = "";// I guess structure should be based on that
    public $body = "";
    public $activity_data = "";// This would probably be the JSON-encoded string
    public $created = "";
    
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
            $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->activity_type = $ret->activity_type;
            $this->activity_subtype = $ret->activity_subtype;
            $this->body = $ret->body;
            $this->activity_data = $ret->activity_data;
            $this->created = $ret->created;
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

    public function getActivityType() {
        return $this->activity_type;
    }

    public function getActivitySubtype() {
        return $this->activity_subtype;
    }

    public function getBody() {
        if ($this->getActivityType() == 'activity') {
            $activity_subtype = $this->getActivitySubtype();
            $body = "";
            $data = $this->getActivityDataArray();
            switch ($activity_subtype) {
                case "join_project":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%1$s joined the project.'), $data);
                    break;
                case "add_task":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added task %1$s'), $data);
                    break;
                case "add_resource":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added resource %1$s'), $data);
                    break;
                default:
                    // TODO Check if this stays that way
                    $data = "NOT IMPLEMENTED";
                    break;
            }
            return $body;
        }
        return $this->body;
    }

    public function getActivityData() {
        return $this->activity_data;
    }

    public function getActivityDataArray() {
        $data = json_decode($this->activity_data);
        if (!is_array($data)) {
            $data = array();
        }
        return $data;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function create($creator, $project_id, $activity_type, $activity_subtype, $body, $activity_data) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $activity_type = mysql_real_escape_string($activity_type);
        $activity_subtype = mysql_real_escape_string($activity_subtype);
        $body = mysql_real_escape_string($body);
        $activity_data = json_encode($activity_data);
        $q = "INSERT INTO " . DB_PREFIX . "activity (creator, project_id, activity_type, activity_subtype, body, activity_data, created) VALUES ($creator, $project_id, '$activity_type', '$activity_subtype', '$body', '$activity_data', NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // TODO Possibly some activity needs to be added
            return $uid;
        }
        return false;
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "activity WHERE id = {$this->id}";
        return query_delete($q);
    }
}
