<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $email = get_input('email');
    $token = get_input('token');
    $password = get_input("new_password");
    $password_confirm = get_input("new_password_confirm");
    
    $input_values = array();
    $input_values['email'] = $email;
    $input_values['token'] = $token;
    $_SESSION['input_values'] = $input_values;

    if (!$TeKe->user->isValidToken($email, $token)) {
        $TeKe->add_system_message(_("Could not verify that this user requested a password reset."), "error");
        forward("password_recovery");
    } else if (empty($password) || empty($password_confirm)) {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), 'error');
        forward("password_reset");
    } else if ($password != $password_confirm) {
	    $TeKe->add_system_message(_("Passwords did not match."), 'error');
	    forward("password_reset");
    } else {
        if ($TeKe->user->reset_password($email, $password)) {
            // TODO also log user in
            $TeKe->add_system_message(_("Your password was changed."));
        } else {
            $TeKe->add_system_message(_("Error occured."), 'error');
            forward("password_reset");
        }
    }

    unset($_SESSION['input_values']);
    forward("");
?>
