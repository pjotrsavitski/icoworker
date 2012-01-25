<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $id = get_input("id");
    $old_password = get_input("old_password");
    $password = get_input("new_password");
    $password_confirm = get_input("new_password_confirm");

    $user = new User($id);

    if (empty($old_password) || empty($password) || empty($password_confirm)) {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), "error");
        forward("user/password/".$user->username);
    } else if (!$TeKe->user->is_password_correct($user, $old_password)) {
        $TeKe->add_system_message(_("Current password was incorrect."), "error");
        forward("user/password/".$user->username);
    } else if ($password != $password_confirm) {
	    $TeKe->add_system_message(_("Passwords did not match."), "error");
	    forward("user/password/".$user->username);
    } else {
        if ($TeKe->user->change_password($user, $password)) {
            $TeKe->add_system_message(_("Your password was changed."));
        } else {
            $TeKe->add_system_message(_("Error occured."), 'error');
            forward("user/password/".$user->username);
        }
    }

    forward("user/view/".$user->username);
?>

