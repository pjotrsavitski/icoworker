<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $task = ProjectManager::getTaskById(get_input('task_id'));
        
        if (!($task instanceof Task)) {
            $TeKe->add_system_message(_("No such task."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $task->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $user = new User(get_input('member_id'));
        if (!$user->getId()) {
            $TeKe->add_system_message(_("No such user."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if (!$task->isAssociatedMember($user->getId())) {
            $TeKe->add_system_message(_("Participant is not associated with this task."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($task->removeAssociatedMember($user)) {
            $response->setStateSuccess();
            $TeKe->add_system_message(sprintf(_("%s removed from task %s."), $user->getFullname(), $task->getTitle()));
            $response->setMessages();
        } else {
            $TeKe->add_system_message(_("Participant could not be removed from task."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
