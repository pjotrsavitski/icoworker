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
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $q = "UPDATE " . DB_PREFIX . "tasks SET title='$title', description='$description' WHERE id = {$task->id}";
        // XXX Activity missing
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "tasks WHERE id = {$this->id}";
        return query_delete($q);
    }
}
