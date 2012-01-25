<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $user_identificator = get_input('user_identificator');
    
    $input_values = array();
    $input_values['user_identificator'] = $user_identificator;
    $_SESSION['input_values'] = $input_values;

	if (empty($user_identificator)) {
		$TeKe->add_system_message(_("Insert your username or email address."), 'error');
        forward("password_recovery");
    } else if (!$TeKe->user->check_username_or_email_exists($user_identificator)) {
    	    $TeKe->add_system_message(_("User with that username or email address does not exists."), "error");
            forward("password_recovery");
    } else {
        $user = $TeKe->user->get_user_by_username_or_email($user_identificator);
        if ($TeKe->user->send_password_reset_mail($user)) {
            $TeKe->add_system_message(_("Mail sent."));
        } else {
            $TeKe->add_system_message(_("Sending mail failed."), 'error');
            forward("password_recovery");
        }
    }

    unset($_SESSION['input_values']);
    forward("");
?>
