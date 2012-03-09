<?php

class Milestone {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $milestone_date = "";
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
            $q = "SELECT * FROM " . DB_PREFIX . "milestones WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->title = $ret->title;
            $this->milestone_date = $ret->milestone_date;
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

    public function getCreator() {
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

    public function getMilestoneDate() {
        return $this->milestone_date;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $title, $milestone_date) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $milestone_date = (int) $milestone_date;
        $q = "INSERT INTO " . DB_PREFIX . "milestones (creator, project_id, title, milestone_date, created, updated) VALUES ($creator, $project_id, '$title', FROM_UNIXTIME('$milestone_date'), NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_milestone', '', array($title, $milestone_date));
            return $uid;
        }
        return false;
    }

    public function update($milestone, $title, $milestone_date) {
        $title = mysql_real_escape_string($title);
        $milestone_date = (int) $milestone_date;
        $q = "UPDATE " . DB_PREFIX . "milestones SET title='$title', milestone_date=FROM_UNIXTIME('$milestone_date') WHERE id = {$milestone->id}";
        // TODO Activity stream missing
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "milestones WHERE id = {$this->id}";
        return query_delete($q);
    }
}
