<?php

class User {
        public $id = NULL;
        public $username = "";
        public $first_name = "";
        public $last_name = "";
        public $email = "";
        public $language = "et";
        public $level = 1;
        
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
                $this->facebook_id = $ret->facebook_id;
                $this->first_name = $ret->first_name;
                $this->last_name = $ret->last_name;
                $this->email = $ret->email;
                $this->language = $ret->language;
                $this->level = $ret->role;
            } else {
                // Set username and id to defaults, this way it is possible to check if loading failed
                $this->id = NULL;
                $this->username = "";
            }
        }
        
        function getId() {
            return $this->id;
        }
        
        function getUsername() {
            return $this->username;
        }

        function getfacebookId() {
            return $this->facebook_id;
        }
        
        function getFirstName() {
            return $this->first_name;
        }
        
        function getLastName() {
            return $this->last_name;
        }
        
        function getFullName() {
            return $this->first_name." ".$this->last_name;
        }
        
        function getEmail() {
            return $this->email;
        }
        
        function getRole() {
            return $this->level;
        }
        
        function getURL() {
            return WWW_ROOT."user/view/".$this->username;
	    }
        
        // TODO possibly not needed
        function getRoles() {
            return $this->roles;
        }

        function getLanguage() {
            return $this->language;
        }
        
        // TODO possibly not needed
        function hasAnyRole($required) {
            $lstr = "";
            foreach ( $required as $r) {
                $lstr .= " ".$r;
            }
            //$psyhvel->out("debug", "checking permissions, must have:".$lstr);
            if ( count($required) == 0) {
                return True;
            } else if ( count($required) == 1 && !$required[0] ) {
                return True;
            }
            foreach ($required as $r) {
                if ( array_key_exists($r, $this->roles) ) {
                    if ( $this->roles[$r] == 1) {
                        return True;
                    }
                }
            }
            //$psyhvel->out("debug", "...failed");
            return False;
        }
        
        // TODO possibly not needed
        function hasRole($role) {
            if ( $this->roles[$role] == 1) {
                return True;
            }
            return False;
        }
        
        function getUserIdByUname($uname) {
            global $db;
            $q = "SELECT id FROM " . DB_PREFIX . "users WHERE uname='".$uname."'";
            $ret = $db->query($q);
            $num = mysql_num_rows($ret);
            if ( $num == 1) { // OK
                $res = mysql_fetch_array($ret);
                return $res['id'];
            }
            return -1;
        }
        
        function getUsers() {
            global $db;
            return $db->query("SELECT *, concat(firstname, ' ', lastname) AS fullname FROM " . DB_PREFIX . "users");
        }
        
        function getUserById($id=false) {
            if (isset($id) && is_numeric($id)) {
                $user = new User();
                $user->load($id);
                return $user;
            }
            return false;
        }
        
        function getAllUsers() {
            $res = query_rows("SELECT *, concat(first_name, ' ', last_name) AS fullname FROM " . DB_PREFIX . "users", 'User');
            return $res;
        }

        // XXX deleteme
        public function make_admin($uid) {
            $roles = "111111";
            $res = $this->db->query("UPDATE " . DB_PREFIX . "users SET roles='{$roles}' WHERE id={$uid}");
            if ($res) return 1;
            return 0;
        }

        // XXX This is not needed
        function isAuthenticationCorrect($username, $password) {
            $res = query("SELECT * FROM "  . DB_PREFIX . "users WHERE username='{$username}'");
            $check = mysql_fetch_array($res);
            if ($this->valid_password($password, $check["password"], $check["salt"])){
               return $check["id"]; 
            }
            return false;
        } 

        // XXX This is not needed
        function authenticate_user($username, $password) {
            $res = query("SELECT * FROM "  . DB_PREFIX . "users WHERE username='{$username}'");
            $check = mysql_fetch_array($res);
            if ($this->valid_password($password, $check["password"], $check["salt"])){
                $this->load($check["id"]);
               return $this;  
            }
        }

        function updateLastLoginTime() {
            $q = "UPDATE " . DB_PREFIX . "users SET last_login=NOW() WHERE id={$this->getId()}";
            return query($q);
        }

        function check_username_exists($username) {
            $res = query("SELECT count(username) FROM " . DB_PREFIX . "users WHERE username='{$username}'");
            $check = mysql_fetch_row($res);
            return $check[0];
        }

        function check_facebook_id_exists($facebook_id) {
            $res = query("SELECT count(facebook_id) FROM " . DB_PREFIX . "users WHERE facebook_id=$facebook_id");
            $check = mysql_fetch_row($res);
            return $check[0];
        }

        // TODO possibly not needed
        function is_valid_username($username) {
            return preg_match('/^[a-zA-Z0-9_]+$/', $username);
        }
        
        function check_email_exists($email) {
            $res = query("SELECT count(email) FROM " . DB_PREFIX . "users WHERE email='{$email}'");
            $check = mysql_fetch_row($res);
            return $check[0];
        }
        
        function is_valid_email($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        function check_username_or_email_exists($identificator) {
            $q = query("SELECT count(*) FROM " . DB_PREFIX . "users WHERE username='{$identificator}' OR email='{$identificator}'");
            $res = mysql_fetch_row($q);
            return $res[0];
        }
        
        function get_user_by_username_or_email($identificator) {
            $res = query_row("SELECT * FROM " . DB_PREFIX . "users WHERE username='{$identificator}' OR email='{$identificator}'");
            if (!$res) {
                return false;
            }
            $this->load($res->id);
            return $this;  
        }

        function get_user_by_facebook_id($facebook_id) {
            $res = query_row("SELECT * FROM " . DB_PREFIX . "users WHERE facebook_id=$facebook_id");
            if (!$res) {
                return false;
            }
            $this->load($res->id);
            return $this;
        }
        
        public function create($username, $email, $facebook_id, $firstname, $lastname) {
            $roles = "000000";
            $q = "INSERT INTO " . DB_PREFIX . "users (username, email, facebook_id, first_name, last_name, registered) values ('$username', '$email', $facebook_id, '$firstname', '$lastname', NOW())";
            $uid = query_insert($q);
            if ($uid) return $uid;
            return 0;
        }
        
        public function update_settings($user, $first_name, $last_name, $email, $language) {
            $q = "UPDATE " . DB_PREFIX . "users SET first_name='{$first_name}', last_name='{$last_name}', email='{$email}', language='{$language}' WHERE id = '{$user->id}'";
            return query($q);
        }

        // XXX unneeded
        private function generate_salt($username) {  
           $salt = sha1('~'.$username.'~'.microtime(TRUE).'~');  
           $salt = substr($salt, rand(0,30), 10);  
           return $salt;  
        }  

        // XXX unneeded
        private function hash_password($password, $salt) {  
            return sha1('~'.$password.'~'.$salt.'~');  
        }  
        
        // XXX unneeded
        private function valid_password($password, $hash, $salt) {
            return $this->hash_password($password, $salt) == $hash;  
        } 

        // XXX unneeded
        private function create_password_reset_token($user) {
            $q = query("SELECT * FROM "  . DB_PREFIX . "users WHERE id='{$user->id}'");
            $res = mysql_fetch_array($q);
            $expiration_time = time() + (24 * 60 * 60);    // expires in 24 hours
            $hash = $res["password"];
            $salt = $res["salt"];
            $token = $this->create_token($expiration_time, $hash, $salt);
            return $token . '-' . $expiration_time;
        }

        // XXX unneeded
        function create_token($expiration_time, $hash, $salt) {
            return sha1('~'.$expiration_time.'~'.$hash.'~'.$salt.'~');
        }

        // XXX unneeded
        function send_password_reset_mail($user) {
            global $TeKe;
            $subject = _("Password reset");
            $token = $this->create_password_reset_token($user);
            $password_link = WWW_ROOT . "password_reset?email={$user->email}&token={$token}";
            $message = "Hi %s,\n\n";
            $message .= "We have received your password reset request.\n\n";
            $message .= "Please visit the following link to reset your password:\n";
            $message .= "%s\n\n";
            $message .= "The link will expire after 24 hours for security reasons.\n\n";
            $message .= "If you did not request this forgotten password email, no action is needed, your password will not be reset as long as the link above is not visited.\n\n";
            $message .= "Thanks,\n";
            $message .= "%s\n\n";
            $message .= "--\n";
            $message .= "Please do not reply to this message.";
            $msg = _($message);
            $msg = sprintf($msg, $user->getFullName(), $password_link, SITE_NAME);
            return $TeKe->send_mail($user, $subject, $msg);
        }

        // XXX unneeded
        public function isValidToken($email, $token) {
            if (!$token) {
                return false;
            }
            $attrs = explode("-", $token);
            $token = $attrs[0];
            $timestamp = $attrs[1];
            if (!$this->isLinkExpired($timestamp)) {
                return false;
            }
            $q = query("SELECT * FROM " . DB_PREFIX . "users WHERE email='{$email}'");
            $res = mysql_fetch_array($q);
            $hash = $res["password"];
            $salt = $res["salt"];
            return $this->create_token($timestamp, $hash, $salt) == $token;
        }

        // XXX possibly unneeded
        function isLinkExpired($timestamp) {
            $current_timestamp = time();
            if ($current_timestamp > $timestamp) {
                return false;
            }
            return true;
        }

        // XXX unneeded
        function reset_password($email, $password) {
            $q = query("SELECT * FROM " . DB_PREFIX . "users WHERE email='{$email}'");
            $res = mysql_fetch_array($q);
            $username = $res["username"];
            $salt = $this->generate_salt($username);
            $hash = $this->hash_password($password, $salt);
            $q = "UPDATE " . DB_PREFIX . "users SET password='{$hash}', salt='{$salt}' WHERE email = '{$email}'";
            return query($q);
        }

        // XXX unneeded
        function change_password($user, $password) {
            $salt = $this->generate_salt($user->username);
            $hash = $this->hash_password($password, $salt);
            $q = "UPDATE " . DB_PREFIX . "users SET password='{$hash}', salt='{$salt}' WHERE id={$user->id}";
            return query($q);
        }
        
        // XXX unneeded
        function is_password_correct($user, $password) {
            $q = query("SELECT * FROM "  . DB_PREFIX . "users WHERE id={$user->id}");
            $res = mysql_fetch_array($q);
            return $this->valid_password($password, $res["password"], $res["salt"]);
        }

        function delete() {
            global $TeKe;
            if (is_numeric($this->getId()) && $this->getId() > 0) {
                if ($TeKe->is_admin()) {
                    return query("DELETE FROM " . DB_PREFIX . "users WHERE id=".$this->getId());
                }
            }
            return false;
        }
    }

?>
