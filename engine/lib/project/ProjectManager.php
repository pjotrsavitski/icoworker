<?php

require_once(dirname(__FILE__).'/Project.php');
require_once(dirname(__FILE__).'/Task.php');
require_once(dirname(__FILE__).'/Resource.php');
require_once(dirname(__FILE__).'/Activity.php');
require_once(dirname(__FILE__).'/Milestone.php');
require_once(dirname(__FILE__).'/Document.php');
require_once(dirname(__FILE__).'/Comment.php');

class ProjectManager {

    public static function getProjects() {
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE 1";
        return query_rows($q, 'Project');
    }

    public static function getUserProjects($user_id) {
        $user_id = (int)$user_id;
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE $user_id IN ( SELECT user_id FROM " . DB_PREFIX . "project_members WHERE project_id = id)";
        return query_rows($q, 'Project');
    }

    public static function getOwnedProjects($user_id) {
        $user_id = (int)$user_id;
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE creator = $user_id";
        return query_rows($q, 'Project');
    }

    public static function getProjectById($project_id) {
        $project_id = (int)$project_id;
        $q = "SELECT * FROM " . DB_PREFIX . "projects WHERE id = $project_id";
        return query_row($q, 'Project');
    }

    public static function getTaskById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE id = $id";
        return query_row($q, "Task");
    }

    public static function getResourceById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE id = $id";
        return query_row($q, "Resource");
    }

    public static function getActivityById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE id = $id";
        return query_row($q, "Activity");
    }
    
    public static function getMessageById($id) {
        return self::getActivityById($id);
    }

    public static function getDocumentById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "documents WHERE id = $id";
        return query_row($q, 'Document');
    }

    public static function getCommentById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "project_comments WHERE id = $id";
        return query_row($q, 'Comment');
    }

    public static function getMilestoneById($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "milestones WHERE id = $id";
        return query_row($q, 'Milestone');
    }

    public static function getProjectTasks($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE project_id = $id ORDER BY id ASC";
        return query_rows($q, 'Task');
    }

    public static function getProjectStandaloneTasks($id) {
        $id = (int)$id;
        $q ="SELECT * FROM " . DB_PREFIX . "tasks WHERE project_id = $id AND is_timelined = 0 ORDER BY id ASC";
        return query_rows($q, 'Task');
    }

    public static function getProjectTimelinedTasks($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "tasks WHERE project_id = $id AND is_timelined = 1 ORDER BY id ASC";
        return query_rows($q, 'Task');
    }

    public static function getProjectResources($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE project_id = $id";
        return query_rows($q, 'Resource');
    }

    public static function getProjectMilestones($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "milestones WHERE project_id = $id";
        return query_rows($q, 'Milestone');
    }

    public static function getProjectDocuments($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "documents WHERE project_id = $id";
        return query_rows($q, 'Document');
    }
    
    public static function getProjectComments($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "project_comments WHERE project_id = $id";
        return query_rows($q, 'Comment');
    }


    public static function getProjectMessages($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id AND activity_type = 'message' ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }

    public static function getProjectActivities($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id AND activity_type = 'activity' ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }

    public static function getProjectActivity($id) {
        $id = (int)$id;
        $q = "SELECT * FROM " . DB_PREFIX . "activity WHERE project_id = $id ORDER BY created DESC";
        return query_rows($q, 'Activity');
    }

    public static function searchForParticipants($project_id, $criteria) {
        $project_id = (int)$project_id;
        $criteria = mysql_real_escape_string($criteria);
        $q = "SELECT * FROM " . DB_PREFIX . "users WHERE (first_name LIKE '%$criteria%' OR last_name LIKE '%$criteria%' OR email = '$criteria') AND id NOT IN ( SELECT user_id FROM " . DB_PREFIX . "project_members WHERE project_id = $project_id)";
        return query_rows($q, 'User');
    }
}
