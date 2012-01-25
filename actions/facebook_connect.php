<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $username = get_input('username');
    $password = get_input('password');
    $fb_user_id = get_input("facebook_user_id");
    $forward_to = WWW_ROOT;
    
	if (!empty($username) && !empty($password) && !empty($facebook_id)) {
        $user_id = $TeKe->user->isAuthenticationCorrect($username, $password);
        if ($user_id) {
            if (!$TeKe->facebook->isAccountConnectedWithFacebook($user_id)) {
                $TeKe->facebook->connectAccountWithFacebook($user_id, $facebook_id);
                $TeKe->user->load($user_id);
                unset($_SESSION["user"]);
                $_SESSION["user"] = $user->id;
            } else {
                $TeKe->add_system_message(_("This account is already connected with different facebook account."), "error");
                $forward_to .= "facebook_connect";
            }
        } else {
            $TeKe->add_system_message(_("Wrong username or password"), "error");
            $forward_to .= "facebook_connect";
        }
	} else {
        $TeKe->add_system_message(_("At least one of the parameters is empty."), "error");
        $forward_to .= "facebook_connect";
	}

    forward($forward_to);
?>
