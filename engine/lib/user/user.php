<?php

    class UserDetails {
        
        private $id;
        private $userid;
        private $username;
        private $firstname = "";
        private $lastname = "";
        private $email = "";
        private $sex = "boy";
        private $homepage = "";
        private $language = DEFAULT_LANGUAGE;
        
        function __construct($uid) {
            global $db;
            $q = "SELECT * FROM " . DB_PREFIX . "userinfo WHERE userid=".$uid;
            $ret = $db->query($q);
            $num = mysql_num_rows($ret);
            if ( $num == 1) { // OK
                $res = mysql_fetch_array($ret);
                $this->id = $res['id'];
                $this->userid = $res['userid'];
                $this->email = $res['email'];
                $this->firstname = $res['firstname'];
                $this->lastname = $res['lastname'];
                $this->homepage = $res['homepage'];
                $this->sex = $res['sex'];
                $this->language = $res['language'];
            } else {
                if ( $uid != -1 ) {
                    $this->create($uid);
                }
            }
        }
        
        function getFullName() {
            return $this->first_name." ".$this->last_name;
        }

        function getFullnameDisplay() {
            $fn = $this->getFullname();
            if ($fn == "" || $fn == NULL || strlen(trim($fn)) == 0 ){
                return $this->username;
            }
            return $fn;
        }

        function setUsername($un) {
            $this->username = $un;
        }

        function getFirstname()  {
            return $this->firstname;
        }
        
        function setFirstname($val) {
            $this->firstname = $val;
        }
        
        function getLastname()  {
            return $this->lastname;
        }
        
        function setLastname($val)  {
            $this->lastname = $val;
        }
        
        function getEmail() {
            return $this->email;
        }
        
        function setEmail($val) {
            $this->email = $val;
        }
        
        function getSex() {
            return $this->sex;
        }
        
        function setSex($val) {
            $this->sex = $val;
        }
        
        function getLanguage() {
            return $this->language;
        }
        
        function setLanguage($val) {
            $this->language = $val;
        }
        
        function getHomepage() {
            return $this->homepage;
        }
        
        function setHomepage($val) {
            $this->homepage = $val;
        }
        
        function update() {
            global $db;
            $q = "UPDATE " . DB_PREFIX . "userinfo SET ";
            $q .= " firstname='".$this->firstname."'";
            $q .= ", lastname='".$this->lastname."'";
            $q .= ", email='".$this->email."'";
            $q .= ", sex='".$this->sex."'";
            $q .= ", homepage='".$this->homepage."'";
            $q .= ", language='".$this->language."'";
            $q .= " WHERE id=".$this->id." AND userid=".$this->userid;
            $db->query($q);
        }
        
        private function create($uid) {
            global $db;
            $q = "INSERT INTO " . DB_PREFIX . "userinfo (userid, sex, language) values (".$uid.", '".$this->sex."', '".$this->language."')";
            $db->query($q);
        }
    }
    
class User {
        public $id = NULL;
        public $username = "Anonymous";
        public $first_name = "";
        public $last_name = "";
        public $email = "";
        public $language = "et";
        public $approved = 0;
        public $details = null;
        public $groups = array();
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
                $this->first_name = $ret->first_name;
                $this->last_name = $ret->last_name;
                $this->email = $ret->email;
                $this->language = $ret->language;
                $this->approved = $ret->approved;
                $this->level = $ret->role;
            }
        }
        
        function getUsername() {
            return $this->username;
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
        
        function getRoles() {
            return $this->roles;
        }
        
        function getGroups() {
            return $this->groups;
        }
        
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
        
        function hasRole($role) {
            if ( $this->roles[$role] == 1) {
                return True;
            }
            return False;
        }
        
        function getDetails() {
            if ($this->details == null) {
                $ud = new UserDetails($this->id);
                $ud->setUsername($this->username);
                $this->details = $ud;
            }
            return $this->details;
        }
        
        function getId() {
            return $this->id;
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
        
        /*function getUserById($usid) {
            global $db;
            $q = "SELECT * FROM " . DB_PREFIX . "users LEFT JOIN userinfo ON " . DB_PREFIX . "users.id=" . DB_PREFIX . "userinfo.userid WHERE " . DB_PREFIX . "users.id=".$usid;
            $ret = $db->query($q);
            $res = mysql_fetch_array($ret);
            return $res;
        }*/
        
        function getUsers() {
            global $db;
            return $db->query("SELECT *, concat(firstname, ' ', lastname) AS fullname FROM " . DB_PREFIX . "users LEFT JOIN " . DB_PREFIX . "userinfo ON " . DB_PREFIX . "users.id=" . DB_PREFIX . "userinfo.userid WHERE approved");
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
            $res = query_rows("SELECT *, concat(first_name, ' ', last_name) AS fullname FROM " . DB_PREFIX . "users");
            return $res;
        }

        function getUnapprovedUsers() {
            $res = query_rows("SELECT * FROM " . DB_PREFIX . "users WHERE approved=0;");
            return $res;
        }
        
        function user_approve($id) {
            $q = "UPDATE " . DB_PREFIX . "users SET approved=1 WHERE id={$id}";
            return query($q);
        }
        
        public function make_admin($uid) {
            $roles = "111111";
            $approved = 1;
            $res = $this->db->query("UPDATE " . DB_PREFIX . "users SET roles='{$roles}', approved={$approved} WHERE id={$uid}");
            if ($res) return 1;
            return 0;
        }

        function isAuthenticationCorrect($username, $password) {
            $res = query("SELECT * FROM "  . DB_PREFIX . "users WHERE username='{$username}' AND approved=1");
            $check = mysql_fetch_array($res);
            if ($this->valid_password($password, $check["password"], $check["salt"])){
               return $check["id"]; 
            }
            return false;
        } 

        function authenticate_user($username, $password) {
            $res = query("SELECT * FROM "  . DB_PREFIX . "users WHERE username='{$username}' AND approved=1");
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
        
        public function create($username, $email, $password, $firstname, $lastname) {
            $salt = $this->generate_salt($username);
            $hash = $this->hash_password($password, $salt);
            $roles = "000000";
            $approved = 0;
            $q = "INSERT INTO " . DB_PREFIX . "users (first_name, last_name, email, username, password, salt, registered) values ('".$firstname."', '".$lastname."', '".$email."', '".$username."', '".$hash."', '".$salt."', NOW())";
            $uid = query_insert($q);
            if ($uid) return $uid;
            return 0;
        }
        
        public function update_settings($user, $first_name, $last_name, $email, $language) {
            $q = "UPDATE " . DB_PREFIX . "users SET first_name='{$first_name}', last_name='{$last_name}', email='{$email}', language='{$language}' WHERE id = '{$user->id}'";
            return query($q);
        }

        private function generate_salt($username) {  
           $salt = sha1('~'.$username.'~'.microtime(TRUE).'~');  
           $salt = substr($salt, rand(0,30), 10);  
           return $salt;  
        }  
        
        private function hash_password($password, $salt) {  
            return sha1('~'.$password.'~'.$salt.'~');  
        }  
        
        private function valid_password($password, $hash, $salt) {
            return $this->hash_password($password, $salt) == $hash;  
        } 

        private function create_password_reset_token($user) {
            $q = query("SELECT * FROM "  . DB_PREFIX . "users WHERE id='{$user->id}'");
            $res = mysql_fetch_array($q);
            $expiration_time = time() + (24 * 60 * 60);    // expires in 24 hours
            $hash = $res["password"];
            $salt = $res["salt"];
            $token = $this->create_token($expiration_time, $hash, $salt);
            return $token . '-' . $expiration_time;
        }

        function create_token($expiration_time, $hash, $salt) {
            return sha1('~'.$expiration_time.'~'.$hash.'~'.$salt.'~');
        }

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

        function isLinkExpired($timestamp) {
            $current_timestamp = time();
            if ($current_timestamp > $timestamp) {
                return false;
            }
            return true;
        }

        function reset_password($email, $password) {
            $q = query("SELECT * FROM " . DB_PREFIX . "users WHERE email='{$email}'");
            $res = mysql_fetch_array($q);
            $username = $res["username"];
            $salt = $this->generate_salt($username);
            $hash = $this->hash_password($password, $salt);
            $q = "UPDATE " . DB_PREFIX . "users SET password='{$hash}', salt='{$salt}' WHERE email = '{$email}'";
            return query($q);
        }

        function change_password($user, $password) {
            $salt = $this->generate_salt($user->username);
            $hash = $this->hash_password($password, $salt);
            $q = "UPDATE " . DB_PREFIX . "users SET password='{$hash}', salt='{$salt}' WHERE id={$user->id}";
            return query($q);
        }
        
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
