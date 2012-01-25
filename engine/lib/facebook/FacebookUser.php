<?php
class FacebookUser {
    private $facebook;
    private $first_name = "";
    private $last_name = "";
    private $email = "";

    function __construct($id=false) {
        $this->setFacebook();
    }
    
    function setFacebook() {
        $facebook = new Facebook(array(
            'appId'  => FACEBOOK_APP_ID,
            'secret' => FACEBOOK_APP_SECRET,
            'cookie' => true,
        ));
        $this->facebook = $facebook;
    }

    function getFacebook() {
        return $this->facebook;
    }
    
    function getFacebookLoginUrl() {
        return $this->getFacebook()->getLoginUrl();
    }

    function getFacebookLogoutUrl() {
        return $this->getFacebook()->getLogoutUrl();
    }

    function getFacebookUser() {
        return $this->getFacebook()->getUser();
    }

    function getFacebookUserProfile() {
        return $this->getFacebook()->api('/me');
    }

    function setUserInfo() {
        $user_info = $this->getFacebookUserProfile();
        $this->first_name = $user_info["first_name"];
        $this->last_name = $user_info["last_name"];
        if (isset($user_info["email"])) {
            $this->email = $user_info["email"];
        }
    }

    function getFirstName() {
        $this->setUserInfo();
        return $this->first_name;
    }

    function getLastName() {
        return $this->last_name;
    }

    function getEmail() {
        return $this->email;
    }


    function doesFacebookUserExists($facebook_id) {
        $res = query("SELECT count(facebook_id) FROM " . DB_PREFIX . "users WHERE facebook_id='{$facebook_id}'");
        $check = mysql_fetch_row($res);
        return $check[0];
    }

    function getUserId($facebook_id) {
        $query = "SELECT id FROM " . DB_PREFIX . "users WHERE facebook_id=".$facebook_id;
        $res = mysql_fetch_row(query($query));
        if (sizeof($res) > 0) {
            return $res[0];
        }
        return false;
    }

    function isAccountConnectedWithFacebook($user_id) {
        $query = "SELECT facebook_id FROM " . DB_PREFIX . "users WHERE id=".$user_id;
        $res = mysql_fetch_row(query($query));
        return $res[0];
    }

    function connectAccountWithFacebook($user_id, $facebook_id) {
        $query = "UPDATE " . DB_PREFIX . "users SET facebook_id=" . $facebook_id . " WHERE id=" . $user_id;
        return query($query);
    }

}
?>
