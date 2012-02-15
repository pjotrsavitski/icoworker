<?php

require_once(dirname(__FILE__).'/Project.php');
require_once(dirname(__FILE__).'/Task.php');
require_once(dirname(__FILE__).'/Resource.php');
require_once(dirname(__FILE__).'/Activity.php');

class ProjectManager {

    public function getProjects() {
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE 1";
        return query_rows($q, 'Project');
    }

    public function getUserProjects($user_id) {
        $user_id = (int)$user_id;
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE creator = $user_id";
        return query_rows($q, 'Project');
    }

    public function getProjectById($project_id) {
        $project_id = (int)$project_id;
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE id = $project_id";
        return query_row($q, 'Project');
    }

    public function getTaskById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE id = $id";
        return query_row($q, "Task");
    }

    public function getResourceById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE id = $id";
        return query_row($q, "Resource");
    }

    public function getActivityById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE id = $id";
        return query_row($q, "Activity");
    }
    
    public function getMessageById($id) {
        return self::getActivityById($id);
    }

    public function getProjectTasks($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE project_id = $id";
        return query_rows($q, 'Task');
    }

    public function getProjectResources($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE project_id = $id";
        return query_rows($q, 'Resource');
    }

    public function getProjectMessages($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id AND activity_type = 'message' ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }

    public function getProjectActivities($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id AND activity_type = 'activity' ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }

    public function getProjectActivity($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }
}
