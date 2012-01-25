<?php

class Group {
        public $id = NULL;
        public $name = "";
        public $language = "et";
        public $examinees = array();
        
        function __construct($id = NULL) {
            if ($id) {
                $this->id = $id;
            }
            $this->load($this->id);
        }

        function load($id = NULL) {
            if (is_numeric($id)) {
                $this->id = $id;
            } else {
                $this->username = $id;
            }
            $ret = false;
            if (is_numeric($this->id)) {
                $q = "SELECT * FROM " . DB_PREFIX . "users WHERE id=".$this->id;
                $ret = query_row($q);
            } else if ($this->username) {
                $q = "SELECT * FROM " . DB_PREFIX . "users WHERE username='".$this->username."'";
                $ret = query_row($q);
            }
            if ( $ret) {
                $this->id = $ret->id;
                $this->username = $ret->username;
                $this->language = $ret->language;
            }
        }
        
        function getUsername() {
            return $this->username;
        }
        
        function getURL() {
            return WWW_ROOT."group/view/".$this->id;
	    }
        
        function getExaminees() {
            return $this->examinees;
        }
        
        function getId() {
            return $this->id;
        }
        
        function getNameById($id) {
            $res = query_row("SELECT name FROM " . DB_PREFIX . "groups WHERE id=".$id);
            return $res->name;
        }
        
        function getGroups() {
            $user_id = $_SESSION['user'];
            $res = query_rows("SELECT * FROM " . DB_PREFIX . "groups");
            return $res;
        }

        function getUserGroups() {
            $user_id = $_SESSION['user'];
            $res = query_rows("SELECT * FROM " . DB_PREFIX . "groups WHERE creator=" . $user_id);
            return $res;
        }

        public function create_examinees($range_start, $range_end) {
            global $TeKe;
            foreach(range($range_start, $range_end) as $number) {
                $password = $TeKe->generate_random_string(6, "0123456789");
                $this->create($number, $password);
            }
            return true;
        }

        public function create($user_id, $group_name) {
            $q = "INSERT INTO " . DB_PREFIX . "groups (creator, name) values (". $user_id .", '". $group_name ."')";
            $uid = query_insert($q);
            if ($uid) return $uid;
            return false;
        }
        
    }

?>
