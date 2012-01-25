<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $forward_to = WWW_ROOT;
    $fb_user_id = $TeKe->facebook->getFacebookUser();
    
    if ($fb_user_id) {
        if ($user_id = $TeKe->facebook->getUserId($fb_user_id)) {
            $TeKe->user->load($user_id);
            unset($_SESSION["user"]);
            $_SESSION["user"] = $user_id;
        } else {
            $forward_to = WWW_ROOT . "facebook_connect";
        }
	}

    forward($forward_to);
?>
