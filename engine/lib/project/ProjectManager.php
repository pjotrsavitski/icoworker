<?php

require_once(dirname(__FILE__).'/Project.php');
require_once(dirname(__FILE__).'/Task.php');
require_once(dirname(__FILE__).'/Resource.php');
require_once(dirname(__FILE__).'/Message.php');

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

    public function getMessageById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "messages WHERE id = $id";
        return query_row($q, "Message");
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
        $q = "SELECT * FROM " . DB_PREFIX . "messages WHERE project_id = $id";
        return query_rows($q, 'Message');
    }
}
