<?php

class Document {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $title = "";
    public $url = "";
    public $notes = "";
    public $created = "";
    public $updated = "";
    public $is_active = 1;
    public $end_date = "";
    
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
            $q = "SELECT * FROM " . DB_PREFIX . "documents WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->title = $ret->title;
            $this->url = $ret->url;
            $this->notes = $ret->notes;
            $this->created = $ret->created;
            $this->updated = $ret->updated;
            $this->is_active = $ret->is_active;
            $this->end_date = $ret->end_date;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getNotes() {
        return nl2br($this->notes);
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

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function isActive() {
        return $this->is_active;
    }

    public function getEndDate() {
        return $this->end_date;
    }

    // Adding version is handled internally
    private function addVersion($creator, $document_id, $title, $url, $notes, $version_type) {
        $now = time();
        $q = "INSERT INTO " . DB_PREFIX . "document_versions (creator, document_id, title, url, notes, version_type, created) VALUES ($creator, $document_id, '$title', '$url', '$notes', $version_type, FROM_UNIXTIME('$now'))";
        $uid = query_insert($q);
        // Finishing the document if needed
        if ($uid && ($version_type == 2 || $version_type == 3)) {
            $q = "UPDATE " . DB_PREFIX . "documents SET is_active = 0, end_date = FROM_UNIXTIME('$now') WHERE id = $document_id";
            query_update($q);
        }
        return $uid;
    }

    public function getVersions() {
        $q = "SELECT * FROM " . DB_PREFIX . "document_versions WHERE document_id = {$this->id} ORDER BY created ASC";
        $versions = query_rows($q);
        if ($versions && is_array($versions) && sizeof($versions) > 0) {
            foreach ($versions as $key => $version) {
                $version->created = format_date_for_js($version->created);
                $version->notes = nl2br($version->notes);
                $versions[$key] = $version;
            }
        }
        return $versions;
    }

    public function create($creator, $project_id, $title, $url, $notes) {
        // Need unescaped data for JSON
        $activity_data = array($title);
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $title = mysql_real_escape_string($title);
        $url = mysql_real_escape_string($url);
        $notes = mysql_real_escape_string($notes);
        $q = "INSERT INTO " . DB_PREFIX . "documents (creator, project_id, title, url, notes, created, updated) VALUES ($creator, $project_id, '$title', '$url', '$notes', NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_document', '', $activity_data);
            // Add version
            Document::addVersion($creator, $uid, $title, $url, $notes, 1);
            return $uid;
        }
        return false;
    }

    public function update($document, $title, $url, $notes, $version_type) {
        // Need unescaped data for JSON
        $activity_data = array($title);
        $title = mysql_real_escape_string($title);
        $url = mysql_real_escape_string($url);
        $notes = mysql_real_escape_string($notes);
        $q = "UPDATE " . DB_PREFIX . "documents SET title='$title', url='$url', notes='$notes' WHERE id = {$document->id}";
        $updated = query_update($q);
        if ($updated) {
            // Add to activity stream
            Activity::create($document->getCreator(), $document->getProjectId(), 'activity', 'add_document_version', '', $activity_data);
            // Add version
            Document::addVersion($document->getCreator(), $document->id, $title, $url, $notes, $version_type);
            return $updated;
        }
        return false;
    }

    public function delete() {
        // TODO Check on how can delete is needed
        // XXX This needs to be protected
        $q = "DELETE FROM " . DB_PREFIX . "documents WHERE id = {$this->id}";
        return query_delete($q);
    }

    public function getVersionTypes($translated = true) {
        return array(
            '1' => $translated ? _('Live') : 'Live',
            '2' => $translated ? _('Ready') : 'Ready',
            '3' => $translated ? _('Dropped') : 'Dropped'
        );
    }
}
