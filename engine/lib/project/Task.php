<?php

class Task {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $description = "";
    public $created = "";
    public $updated = "";
    public $start_date = "";
    public $end_date = "";
    public $is_timelined = 0;
    
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
            $this->start_date = $ret->start_date;
            $this->end_date = $ret->end_date;
            $this->is_timelined = $ret->is_timelined;
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

    public function getStartDate() {
        return $this->start_date;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    public function isTimelined() {
        return $this->is_timelined;
    }

    public function isAssociatedMember($user_id) {
        $user_id = (int) $user_id;
        $q = "SELECT COUNT(*) AS count FROM " . DB_PREFIX . "task_members WHERE task_id = {$this->getId()} AND user_id = $user_id";
        $ret = query_row($q);
        if ($ret) {
            if ((int)$ret->count > 0) {
                return true;
            }
        }
        return false;
    }

    public function addAssociatedMember($user) {
        $q = "INSERT INTO " . DB_PREFIX . "task_members (task_id, user_id) VALUES ({$this->getId()}, {$user->getId()})";
        $result = query_insert($q);
        $result = $this->isAssociatedMember($user->getId());
        if ($result) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'add_task_member', '', array($this->getTitle(), $user->getFullName()));
        }
        return $result;
    }

    public function removeAssociatedMember($user) {
        $q = "DELETE FROM " . DB_PREFIX . "task_members WHERE task_id = {$this->getId()} AND user_id = {$user->getId()}";
        $result = query_delete($q);
        if ($result) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'remove_task_member', '', array($this->getTitle(), $user->getFullName()));
        }
        return $result;
    }

    public function getAssociatedMembers() {
        $q = "SELECT * FROM " . DB_PREFIX . "users WHERE id IN (SELECT user_id FROM " . DB_PREFIX . "task_members WHERE task_id = {$this->getId()})";
        return query_rows($q, 'User');
    }
    
    public function isAssociatedResource($resource_id) {
        $resource_id = (int) $resource_id;
        $q = "SELECT COUNT(*) AS count FROM " . DB_PREFIX . "task_resources WHERE task_id = {$this->getId()} AND resource_id = $resource_id";
        $ret = query_row($q);
        if ($ret) {
            if ((int)$ret->count > 0) {
                return true;
            }
        }
        return false;
    }

    public function addAssociatedResource($resource) {
        $q = "INSERT INTO " . DB_PREFIX . "task_resources (task_id, resource_id) VALUES ({$this->getId()}, {$resource->getId()})";
        $result = query_insert($q);
        $result = $this->isAssociatedResource($resource->getId());
        if ($result) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'add_task_resource', '', array($this->getTitle(), $resource->getTitle()));
        }
        return $result;
    }

    public function removeAssociatedResource($resource) {
        $q = "DELETE FROM " . DB_PREFIX . "task_resources WHERE task_id = {$this->getId()} AND resource_id = {$resource->getId()}";
        $result = query_delete($q);
        if ($result) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'remove_task_resource', '', array($this->getTitle(), $resource->getTitle()));
        }
        return $result;
    }

    public function getAssociatedResources() {
        $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE id IN (SELECT resource_id FROM " . DB_PREFIX . "task_resources WHERE task_id = {$this->getId()})";
        return query_rows($q, 'Resource');
    }

    public function addToTimeline($start_date, $end_date) {
        $q = "UPDATE " . DB_PREFIX . "tasks SET start_date=FROM_UNIXTIME('$start_date'), end_date=FROM_UNIXTIME('$end_date'), is_timelined=1 WHERE id = {$this->getId()}";
        $updated = query_update($q);
        if ($updated) {
            // Add activity to stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'add_task_to_timeline', '', array($this->getTitle(), $start_date, $end_date));
            return $updated;
        }
        return $updated;
    }

    public function removeFromTimeline() {
        $q = "UPDATE " . DB_PREFIX . "tasks SET is_timelined=0 WHERE id = {$this->getId()}";
        $updated = query_update($q);
        if ($updated) {
            // Add actvity to stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'remove_task_from_timeline', '', array($this->getTitle()));
            return $updated;
        }
        return $updated;
    }

    public function create($creator, $project_id, $title, $description) {
        // Need unescaped data for JSON
        $activity_data = array($title);
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $q = "INSERT INTO " . DB_PREFIX . "tasks (creator, project_id, title, description, created, updated) VALUES ($creator, $project_id, '$title', '$description', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_task', '', $activity_data);
            return $uid;
        }
        return false;
    }

    public function update($task, $title, $description) {
        $activity_data = array($title);
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $q = "UPDATE " . DB_PREFIX . "tasks SET title='$title', description='$description', updated=NOW() WHERE id = {$task->id}";
        $updated = query_update($q);
        if ($updated) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $task->getProjectId(), 'activity', 'edit_task', '', $activity_data);
        }
        return $updated;
    }

    public function delete() {
        $q = "DELETE FROM " . DB_PREFIX . "tasks WHERE id = {$this->id}";
        $deleted = query_delete($q);
        if ($deleted) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'delete_task', '', array($this->title));
        }
        return $deleted;
    }
}
