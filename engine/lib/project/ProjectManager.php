<?php

require_once(dirname(__FILE__).'/Project.php');

class ProjectManager {

    public function getProjects() {
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE 1";
        return query_rows($q, 'Project');
    }

    public function getUserProjects($user_id) {
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE creator = $user_id";
        return query_rows($q, 'Project');
    }

    public function getProjectById($project_id) {
        $q = "SELECY * FROM " . DB_PREFIX . "projects WHERE id = $id";
        return query_row($q, 'Project');
    }
}
