<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $id = get_input("id");
    $first_name = get_input("first_name");
    $last_name = get_input("last_name");
    $email = get_input("email");
    $language = get_input("language");

    $input_values = array();
    $input_values['first_name'] = $first_name;
    $input_values['last_name'] = $last_name;
    $input_values['email'] = $email;
    $input_values['language'] = $language;
    $_SESSION['input_values'] = $input_values;

    $user = new User($id);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($language)) {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), "error");
        forward("user/settings/".$user->username);
    } else if (!$TeKe->user->is_valid_email($email)) {
        $TeKe->add_system_message(_("Email is not valid."), "error");
        forward("user/settings/".$user->username);
    } else if ($user->email != $email && $TeKe->user->check_email_exists($email)) {
        $TeKe->add_system_message(_("User with that email already exists."), "error");
        forward("user/settings/".$user->username);
    } else {
        if ($TeKe->user->update_settings($user, $first_name, $last_name, $email, $language)) {
            $TeKe->add_system_message(_("Settings saved."));
        } else {
            $TeKe->add_system_message(_("Error occured."), 'error');
            forward("user/settings/".$user->username);
        }
    }

    unset($_SESSION['input_values']);
    forward("user/view/".$user->username);
?>

