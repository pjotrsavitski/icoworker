<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");
    
    global $TeKe;

    $facebook_user = $TeKe->facebook->getUser();

    if ($facebook_user) {
        try {
            $user_profile = $TeKe->facebook->api("/me");
            $exists = $TeKe->user->check_facebook_id_exists($user_profile['id']);
            if (!$exists) {
                $TeKe->user->create($user_profile['username'], $user_profile['email'], $user_profile['id'], $user_profile['first_name'], $user_profile['last_name']);
                // XXX probably need to check if user got created
            }
            $user = $TeKe->user->get_user_by_facebook_id($user_profile['id']);
            if ($user) {
                $user->updateLastLoginTime();
                unset($_SESSION['user']);
                $_SESSION['user'] = $user->id;
            } else {
                $TeKe->add_system_message(_("Login failed."), 'error');
            }
        } catch (FacebookApiException $e) {
            $TeKe->add_system_message(_("Facebook profile could not be loaded."), 'error');
        }
    } else {
        $TeKe->add_system_message(_("Facebook login failed."), 'error');
    }

    forward(WWW_ROOT);
?>
