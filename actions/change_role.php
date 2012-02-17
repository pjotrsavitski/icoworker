<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_admin_logged_in()) {
        $response = new AJAXResponse();

        $user = new User(get_input('user_id'));
        $role = (int)get_input('role');
        $roles = $TeKe->getAvailableRoles();

        if (!$user->getId()) {
            $TeKe->add_system_message(_("No such user."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($user->getId() == get_logged_in_user_id()) {
            $TeKe->add_system_message(_("Can not change own role."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if (!array_key_exists($role, $roles)) {
            $TeKe->add_system_message(_("No such role."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if (query_update("UPDATE " . DB_PREFIX . "users SET role = $role WHERE id={$user->getId()}")) {
            $TeKe->add_system_message(_("Role changed."));
            $response->setStateSuccess();
        } else {
            $TeKe->add_system_message(_("Role could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
