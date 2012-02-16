<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $project = ProjectManager::getProjectById(get_input('project_id'));

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $user = new User(get_input('user_id'));
        if (!$user->getId()) {
            $TeKe->add_system_message(_("No such user."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($project->isMember($user->getId())) {
            $TeKe->add_system_message(_("Already is a participant."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($project->addMember($user->getId())) {
            $response->setStateSuccess();
            $TeKe->add_system_message(sprintf(_("%s added to participants."), $user->getFullname()));
            $response->setMessages();
        } else {
            $TeKe->add_system_message(_("Participant could not be added."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
