<?php

class Comment {
    public $id = NULL;
    public $creator = NULL;
    public $project_id = NULL;
    public $content = "";
    public $comment_date = "";
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
            $q = "SELECT * FROM " . DB_PREFIX . "project_comments WHERE id = {$this->id}";
            $ret = query_row($q);
        }
        if ($ret) {
            $this->id = $ret->id;
            $this->creator = $ret->creator;
            $this->project_id = $ret->project_id;
            $this->content = $ret->content;
            $this->comment_date = $ret->comment_date;
            $this->created = $ret->created;
            $this->updated = $ret->updated;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getContent() {
        return nl2br($this->content);
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

    public function getCommentDate() {
        return $this->comment_date;
    }

    public function getCreated() {
        // TODO possibly formatting is needed
        return $this->created;
    }

    public function getUpdated() {
        // TODO possibly formatting is needed
        return $this->updated;
    }

    public function create($creator, $project_id, $content, $comment_date) {
        $creator = (int)$creator;
        $project_id = (int)$project_id;
        $content = mysql_real_escape_string($content);
        $comment_date = (int) $comment_date;
        $q = "INSERT INTO " . DB_PREFIX . "project_comments (creator, project_id, content, comment_date, created, updated) VALUES ($creator, $project_id, '$content', FROM_UNIXTIME('$comment_date'), NOW(), NOW())";
        $uid = query_insert($q);
        if ($uid) {
            // Add to activity stream
            Activity::create($creator, $project_id, 'activity', 'add_comment', '', array($comment_date));
            return $uid;
        }
        return false;
    }

    public function update($comment, $content, $comment_date) {
        $content = mysql_real_escape_string($content);
        $comment_date = (int) $comment_date;
        $q = "UPDATE " . DB_PREFIX . "project_comments SET content='$content', comment_date=FROM_UNIXTIME('$comment_date') WHERE id = {$comment->id}";
        $updated = query_update($q);
        if ($updated) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $comment->getProjectId(), 'activity', 'edit_comment', '', array($comment_date));
            return $updated;
        }
        return false;
    }

    public function delete() {
        $q = "DELETE FROM " . DB_PREFIX . "project_comments WHERE id = {$this->id}";
        $deleted = query_delete($q);
        if ($deleted) {
            // Add to activity stream
            Activity::create(get_logged_in_user_id(), $this->getProjectId(), 'activity', 'delete_comment', '', array(strtotime($this->comment_date)));
        }
        return $deleted;
    }
}
