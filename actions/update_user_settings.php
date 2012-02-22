<?php

require_once(dirname(dirname(__FILE__))."/engine/engine.php");

global $TeKe;

if ($TeKe->is_logged_in()) {
    $fields = array('id' => true, 'first_name' => true, 'last_name' => true, 'email' => true, 'language' => true);
    $inputs = array();
    $required_inputs_missing = false;

    foreach ($fields as $key => $requirement) {
        $inputs[$key] = get_input($key);
        if ($key == 'language') {
            // Fall back to default in case an unsupported language is provided
            $available_languages = $TeKe->getAvailableLanguages();
            if (!array_key_exists($inputs[$key], $available_languages)) {
                $available_lang_keys = array_keys($available_languages);
                $inputs[$key] = $available_lang_keys[0];
            }
        }
        if ($requirement) {
            if (empty($inputs[$key])) {
                $required_inputs_missing = true;
            }
        }
    }

    $_SESSION['input_values'] = $inputs;

    $user = new User($inputs['id']);

    if (!$user->getId()) {
        $TeKe->add_system_message(_("No such user."), 'error');
        unset($_SESSION['input_values']);
        forward("");
    }

    if ($TeKe->is_admin() || (get_logged_in_user_id() == $user->getId())) {
        if ($required_inputs_missing) {
            $TeKe->add_system_message(_("At least one of the parameters is empty."), "error");
            forward("user/settings/{$user->username}");
        } elseif (!$TeKe->user->is_valid_email($inputs['email'])) {
            $TeKe->add_system_message(_("Email is not valid."), "error");
            forward("user/settings/{$user->username}");
        } elseif ($user->email != $inputs['email'] && $TeKe->user->check_email_exists($inputs['email'])) {
            $TeKe->add_system_message(_("User with that email already exists."), "error");
            forward("user/settings/{$user->username}");
        } else {
            if ($TeKe->user->update_settings($user, $inputs['first_name'], $inputs['last_name'], $inputs['email'], $inputs['language'])) {
                $TeKe->add_system_message(_("Settings saved."));
            } else {
                $TeKe->add_system_message(_("Error occured."), 'error');
            }
        }
    } else {
        $TeKe->add_system_message(_("Not allowed to change settings for that user."), 'error');
    }

    unset($_SESSION['input_values']);
    forward("user/view/{$user->username}");
}

forward("");
