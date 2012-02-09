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
        return $this->resurce_type;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $title, $description, $url, $resource_type) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $url = mysql_real_escape_string($url);
        $q = "INSERT INTO " . DB_PREFIX . "resources (creator, project_id, title, description, url, resource_type, created, updated) VALUES ($creator, $project_id, '$title', '$description', '$url', '$resource_type', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // TODO Possibly some activity needs to be added
            return $uid;
        }
        return false;
    }

    public function update($resource, $title, $description, $url, $resurce_type) {
        $title = mysql_real_escape_string($title);
        $description = mysql_real_escape_string($description);
        $url = mysql_real_escape_string($url);
        $q = "UPDATE " . DB_PREFIX . "resources SET title='$title', description='$description', url='$url', resource_type='$resource_type' WHERE id = {$resource->id}";
        return query_update($q);
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "resources WHERE id = {$this->id}";
        return query_delete($q);
    }
}