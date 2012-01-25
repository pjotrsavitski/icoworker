<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $first_name = get_input('first_name');
    $last_name = get_input('last_name');
    $username = get_input('username');
    $email = get_input('email');
    $password = get_input("password");
    $password_confirm = get_input("password_confirm");
    $facebook_user_id = get_input("facebook_user_id");
    $forward_to = "register";
    if ($facebook_user_id) {
        $forward_to = "facebook_connect";
    }

    $input_values = array();
    $input_values['first_name'] = $first_name;
    $input_values['last_name'] = $last_name;
    $input_values['username'] = $username;
    $input_values['email'] = $email;
    $_SESSION['input_values'] = $input_values;

	if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), 'error');
        forward($forward_to);
    } else if (!$TeKe->user->is_valid_username($username)) {
        $TeKe->add_system_message(_("Username cannot contain special symbols."), "error");
        forward($forward_to);
    } else if ($TeKe->user->check_username_exists($username)) {
    	$TeKe->add_system_message(_("User with that username already exists."), "error");
        forward($forward_to);
    } else if (!$TeKe->user->is_valid_email($email)) {
        $TeKe->add_system_message(_("Email is not valid."), "error");
        forward($forward_to);
    } else if ($TeKe->user->check_email_exists($email)) {
        $TeKe->add_system_message(_("User with that email already exists."), "error");
        forward($forward_to);
    } else if ($password != $password_confirm) {
	    $TeKe->add_system_message(_("Passwords did not match."), 'error');
	    forward($forward_to);
    } else {
        if ($user_id = $TeKe->user->create($username, $email, $password, $first_name, $last_name)) {
            if ($facebook_user_id) {
                $TeKe->facebook->connectAccountWithFacebook($user_id, $facebook_user_id);
            }
            $TeKe->add_system_message(_("Success"));
        } else {
            $TeKe->add_system_message(_("Registration failed."), 'error');
            forward($forward_to);
        }
    }

    unset($_SESSION['input_values']);
    forward("");
?>
