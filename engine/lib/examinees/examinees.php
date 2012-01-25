<?php

class Examinees {
        public $id = NULL;
        public $username = "Anonymous";
        public $password = "";
        public $language = "et";
        public $groups = array();
        
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
                $q = "SELECT * FROM " . DB_PREFIX . "examinees WHERE id=".$this->id;
                $ret = query_row($q);
            } else if ($this->username) {
                $q = "SELECT * FROM " . DB_PREFIX . "examinees WHERE username='".$this->username."'";
                $ret = query_row($q);
            }
            if ( $ret) {
                $this->id = $ret->id;
                $this->username = $ret->username;
                $this->language = $ret->language;
            }
        }
        
        function authenticate_examinee($username, $password) {
            $check = query_row("SELECT * FROM "  . DB_PREFIX . "examinees WHERE username='{$username}' AND password='{$password}'");
            if ($check) {
                $this->load($check->id);
                return $this;  
            }
        }
        
        function getUsername() {
            return $this->username;
        }
        
        function getURL() {
            return WWW_ROOT."examinees/view/".$this->username;
	    }
        
        /*function getGroups() {
            return $this->groups;
        }*/
        
        function getId() {
            return $this->id;
        }

        /*function getUserById($id=false) {
            if (isset($id) && is_numeric($id)) {
                $user = new User();
                $user->load($id);
                return $user;
            }
            return false;
        }*/
        
        function getGroups() {
            $q = "SELECT group_id FROM " . DB_PREFIX . "group_relations WHERE examinee_id=". $this->getId();
            $relations = query_rows($q);
            $group_ids = array();
            foreach($relations as $rel) {
                $group_ids []= $rel->group_id;
            }
            return $group_ids;
        }

        function getAllExaminees() {
            $res = query_rows("SELECT * FROM " . DB_PREFIX . "examinees");
            return $res;
        }

        function searchExaminees($search_parameters) {
            if (array_key_exists("group_id", $search_parameters)) {
                $group_id = $search_parameters["group_id"];
                $q = "SELECT e.* FROM ". DB_PREFIX ."examinees e INNER JOIN ". DB_PREFIX ."group_relations rel on e.id = rel.examinee_id WHERE rel.group_id = ". $group_id; 
                return query_rows($q);
            } else {
                return $this->getAllExaminees();
            }
        }

        public function create_examinees($range_start, $range_end, $group_id) {
            global $TeKe;
            foreach(range($range_start, $range_end) as $number) {
                $password = $TeKe->generate_random_string(6, "0123456789");
                $examinee_id = $this->create($number, $password);
                $this->addExamineeToGroup($group_id, $examinee_id);
            }
            return true;
        }

        function addExamineeToGroup($group_id, $examinee_id) {
            $q = "INSERT INTO " . DB_PREFIX . "group_relations (group_id, examinee_id) values ('".$group_id."', '".$examinee_id."')";
            $uid = query_insert($q);
            if ($uid) return $uid;
            return false;
        } 

        public function create($username, $password) {
            $q = "INSERT INTO " . DB_PREFIX . "examinees (username, password) values ('".$username."', '".$password."')";
            $uid = query_insert($q);
            if ($uid) return $uid;
            return false;
        }

        function delete() {
            global $TeKe;
            if (is_numeric($this->getId()) && $this->getId() > 0) {
                if ($TeKe->is_logged_in()) {
                    return query("DELETE FROM " . DB_PREFIX . "examinees WHERE id=".$this->getId());
                }
            }
            return false;
        }
        
    }

?>
