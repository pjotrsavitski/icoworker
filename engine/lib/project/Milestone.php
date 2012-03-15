<?php

class Milestone {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $milestone_date = "";
    public $flag_color = "1";
    public $notes = "";
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
            $this->flag_color = $ret->flag_color;
            $this->notes = $ret->notes;
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

    public function getFlagColor() {
        return $this->flag_color;
    }

    public function getFlagColorURL() {
        $color = $this->getFlagColor();
        // Using non-translatable variant so that URL would be correct
        $colors = $this->getAvailableColors(false);
        if (!array_key_exists($color, $colors)) {
            $color = '1';
        }
        return WWW_ROOT . "views/graphics/flag_" . strtolower($colors[$color]) . ".png";
    }

    public function getNotes() {
        return nl2br($this->notes);
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $title, $milestone_date, $flag_color, $notes) {
        // Need unescaped data for JSON
        $activity_data = array($title, $milestone_date);
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $milestone_date = (int) $milestone_date;
        $flag_color = (int) $flag_color;
        $notes = mysql_real_escape_string($notes);
        $q = "INSERT INTO " . DB_PREFIX . "milestones (creator, project_id, title, milestone_date, flag_color, notes, created, updated) VALUES ($creator, $project_id, '$title', FROM_UNIXTIME('$milestone_date'), $flag_color, '$notes', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_milestone', '', $activity_data);
            return $uid;
        }
        return false;
    }

    public function update($milestone, $title, $milestone_date, $flag_color, $notes) {
        // Need unescaped data for JSON
        $activity_data = array($title, $milestone_date);
        $title = mysql_real_escape_string($title);
        $milestone_date = (int) $milestone_date;
        $flag_color = (int) $flag_color;
        $notes = mysql_real_escape_string($notes);
        $q = "UPDATE " . DB_PREFIX . "milestones SET title='$title', milestone_date=FROM_UNIXTIME('$milestone_date'), flag_color=$flag_color, notes='$notes' WHERE id = {$milestone->id}";
        $updated = query_update($q);
        if ($updated) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $milestone->getProjectId(), 'activity', 'edit_milestone', '', $activity_data);
            return $updated;
        }
        return false;
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "milestones WHERE id = {$this->id}";
        return query_delete($q);
    }

    public function getAvailableColors($translated = true) {
        return array(
            '1' => $translated ? _('Red') : 'Red',
            '2' => $translated ? _('Violet') : 'Violet',
            '3' => $translated ? _('Green') : 'Green',
            '4' => $translated ? _('Blue') : 'Blue',
            '5' => $translated ? _('Orange'): 'Orange',
            '6' => $translated ? _('Black') : 'Black'
        );
    }
}
