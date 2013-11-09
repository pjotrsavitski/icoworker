<?php

class Resource {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $description = "";
    public $url = "";
    public $resource_type = "document";
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
            $q = "SELECT * FROM " . DB_PREFIX . "resources WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->title = $ret->title;
            $this->description = $ret->description;
            $this->url = $ret->url;
            $this->created = $ret->created;
            $this->updated = $ret->updated;
            $this->resource_type = $ret->resource_type;
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

    public function getUrl() {
        return $this->url;
    }

    public function getResourceType() {
        return $this->resource_type;
    }

    public function getResourceTypeURL() {
        $type = $this->getResourceType();
        if (!array_key_exists($type, $this->getResourceTypes())) {
            $type = 'default';
        }
        return WWW_ROOT . "views/graphics/resource_{$type}.png";
    }

    public static function getResourceTypes() {
        return array(
            1 => _('Default Document'),
            2 => _('Text'),
            3 => _('Spreadsheet'),
            4 => _('Presentation')
        );
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public static function create($creator, $project_id, $title, $description, $url, $resource_type) {
        // Need unescaped data for JSON
        $activity_data = array($title);
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $url = mysql_real_escape_string($url);
        $resource_type = mysql_real_escape_string($resource_type);
        $q = "INSERT INTO " . DB_PREFIX . "resources (creator, project_id, title, description, url, resource_type, created, updated) VALUES ($creator, $project_id, '$title', '$description', '$url', '$resource_type', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
             // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_resource', '', $activity_data);
            return $uid;
        }
        return false;
    }

    public static function update($resource, $title, $description, $url, $resource_type) {
        $activity_data = array($title);
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $url = mysql_real_escape_string($url);
        $resource_type = mysql_real_escape_string($resource_type);
        $q = "UPDATE " . DB_PREFIX . "resources SET title='$title', description='$description', url='$url', resource_type='$resource_type', updated=NOW() WHERE id = {$resource->id}";
        $updated = query_update($q);
        if ($updated) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $resource->getProjectId(), 'activity', 'edit_resource', '', $activity_data);
        }
        return $updated;
    }

    public function delete() {
        $q = "DELETE FROM " . DB_PREFIX . "resources WHERE id = {$this->id}";
        $deleted = query_delete($q);
        if ($deleted) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'delete_resource', '', array($this->title));
        }
        return $deleted;
    }
}
