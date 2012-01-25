<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $username = get_input('username');
    $password = get_input('password');
    $language = get_input('language', false);
    $forward_to = WWW_ROOT;

	if (!empty($username) && !empty($password)) {
        if ($user = $TeKe->user->authenticate_user($username, $password)) {
            $TeKe->user->updateLastLoginTime();
            unset($_SESSION["user"]);
            if ($language) $user->language = $language;
            $_SESSION["user"] = $user->id;
        } else if (EXAMINEES) {
            if ($examinee = $TeKe->examinee->authenticate_examinee($username, $password)) {
                unset($_SESSION["examinee"]);
                if ($language) $examinee->language = $language;
                $_SESSION["examinee"] = $examinee->id;
                $forward_to = WWW_ROOT."test";
            }
		} else {
			$TeKe->add_system_message(_("Could not log in."), 'error');
		}
	} else {
		$TeKe->add_system_message(_("At least one of the parameters is empty."), 'error');
	}

    forward($forward_to);
?>
