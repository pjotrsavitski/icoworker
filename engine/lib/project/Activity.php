<?php

class Activity {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $activity_type = "";// Either "activity" or "message"
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
                case "edit_project":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%1$s changed the project.'), $data);
                    break;
                case "edit_project_start_date":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[0] = date('d.m.Y', $data[0]);
                    $data[1] = date('d.m.Y', $data[1]);
                    $body = vsprintf(_('%3$s changed the project start date from %1$s to %2$s.'), $data);
                    break;
                case "edit_project_end_date":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[0] = date('d.m.Y', $data[0]);
                    $data[1] = date('d.m.Y', $data[1]);
                    $body = vsprintf(_('%3$s changed the project end date from %1$s to %2$s.'), $data);
                    break;
                case "join_project":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%1$s joined the project.'), $data);
                    break;
                case "leave_project":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%1$s left the project.'), $data);
                    break;
                case "add_task":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added task <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "add_task_to_timeline":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[1] = date('d.m.Y', $data[1]);
                    $data[2] = date('d.m.Y', $data[2]);
                    $body = vsprintf(_('%4$s added task <span class="activity-item-title">%1$s</span> to timeline from %2$s until %3$s'), $data);
                    break;
                case "remove_task_from_timeline":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s removed task <span class="activity-item-title">%1$s</span> from timeline.'), $data);
                    break;
                case "add_resource":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added resource <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "add_milestone":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[1] = date('d.m.Y', $data[1]);
                    $body = vsprintf(_('%3$s added milestone <span class="activity-item-title">%1$s</span> at %2$s'), $data);
                    break; 
                case "edit_milestone":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[1] = date('d.m.Y', $data[1]);
                    $body = vsprintf(_('%3$s updated milestone <span class="activity-item-title">%1$s</span> at %2$s'), $data);
                    break;
                case "delete_milestone":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s deleted milestone <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "add_document":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added document <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "add_document_version":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $body = vsprintf(_('%2$s added document version  <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "add_comment":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[0] = date('d.m.Y', $data[0]);
                    $body = vsprintf(_('%2$s added comment at %1$s'), $data);
                    break;
                case "edit_comment":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[0] = date('d.m.Y', $data[0]);
                    $body = vsprintf(_('%2$s changed comment at %1$s'), $data);
                    break;
                case "delete_comment":
                    $data[] = $this->getCreatorObject()->getFullname();
                    $data[0] = date('d.m.Y', $data[0]);
                    $body = vsprintf(_('%2$s deleted comment at %1$s'), $data);
                    break;
                case "add_task_member":
                    $body = vsprintf(_('Task <span class="activity-item-title">%1$s</span> assigned to <span class="activity-item-title">%2$s</span>'), $data);
                    break;
                case "remove_task_member":
                    $body = vsprintf(_('Task <span class="activity-item-title">%1$s</span> assignment removed from <span class="activity-item-title">%2$s</span>'), $data);
                    break;
                case "add_task_resource":
                    $body = vsprintf(_('Resource <span class="activity-item-title">%2$s</span> assigned to task <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                case "remove_task_resource":
                    $body = vsprintf(_('Resource <span class="activity-item-title">%2$s</span> is no longer assigned to task <span class="activity-item-title">%1$s</span>'), $data);
                    break;
                default:
                    // TODO Check if this stays that way
                    $body = "NOT IMPLEMENTED";
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
        // NB! Information inside $activity_data Array should be UNESCAPED
        // Need to escape resulting JSON string
        $activity_data = mysql_real_escape_string(json_encode($activity_data));
        $q = "INSERT INTO " . DB_PREFIX . "activity (creator, project_id, activity_type, activity_subtype, body, activity_data, created) VALUES ($creator, $project_id, '$activity_type', '$activity_subtype', '$body', '$activity_data', NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // TODO Possibly some activity needs to be added
            return $uid;
        }
        return false;
    }

    /*public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "activity WHERE id = {$this->id}";
        return query_delete($q);
    }*/
}
