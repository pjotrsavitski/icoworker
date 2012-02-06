<?php

class Project {
    public $id = NULL;
    public $creator = NULL;
    public $title = "";
    public $goal = "";
    public $start_date = "";
    public $end_date = "";
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
            $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            // TODO Consider just running a for cycle on attributes
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->title = $ret->title;
            $this->goal = $ret->goal;
            $this->start_date = $ret->start_date;
            $this->end_date = $ret->end_date;
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

    public function getGoal() {
        return $this->goal;
    }

    public function getStartDate() {
        // TODO possibly formatting is needed
        return $this->start_date;
    }

    public function getEndDate() {
        // TODO possibly formatting is needed
        return $this->end_date;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function getURL() {
        return WWW_ROOT . "project/view/{$this->id}";
    }

    public function isMember($user_id) {
        $q = "SELECT COUNT(*) as count FROM " . DB_PREFIX . "project_members WHERE project_id={$this->id} AND user_id=$user_id";
        $ret = query_row($q);
        if ($ret) {
            if ((int)$ret->count > 0) {
                return true;
            }
        }
        return false;
    }

    public function addMember($user_id) {
        $q = "INSERT INTO " . DB_PREFIX . "project_members (project_id, user_id) VALUES ({$this->id}, $user_id)";
        return query_insert($q);
    }

    public function removeMember($user_id) {
        // Creator can not leave the project
        if ($this->creator == $user_id) {
            return false;
        }
        $q = "DELETE FROM " . DB_PREFIX . "project_members WHERE project_id={$this->id} AND user_id=$user_id";
        return query_delete($q);
    }

    public function getMembers() {
        $q = "SELECT * FROM " . DB_PREFIX . "users WHERE id IN (SELECT user_id FROM " . DB_PREFIX . "project_members WHERE project_id = {$this->id})";
        return query_rows($q, 'User');
    }

    public function getMembersCount() {
        $q = "SELECT COUNT(*) as count FROM " . DB_PREFIX . "project_members WHERE project_id={$this->id}";
        $result = query_row($q);
        if ($result && ((int)$result->count > 0)) {
            return (int)$result->count;
        }
        return 0;
    }

    public function create($creator, $title, $goal, $start_date, $end_date) {
        $creator = (int)$creator;
        $title = mysql_real_escape_string($title);
        $goal = mysql_real_escape_string($goal);
        // TODO Check if start_date and end_date need to be processed
        $q = "INSERT INTO " . DB_PREFIX . "projects (creator, title, goal, start_date, end_date, created, updated) VALUES ($creator, '$title', '$goal', FROM_UNIXTIME('$start_date'), FROM_UNIXTIME('$end_date'), NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add project creator to members
            $project = new Project($uid);
            $project->addMember($creator);
            return $uid;
        }
        return false;
    }

    public function update($project, $title, $goal, $start_date, $end_date) {
        $title = mysql_real_escape_string($title);
        $goal = mysql_real_escape_string($goal);
        // TODO Check if start_date and end_date need to be processed
        $q = "UPDATE " . DB_PREFIX . "projects SET title='$title', goal='$goal', start_date='$start_date', end_date='$end_date' WHERE id = {$project->id}";
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        $q = "DELETE FROM " . DB_PREFIX . "projects WHERE id = {$this->id}";
        return query_delete($q);
    }
}
