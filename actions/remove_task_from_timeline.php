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

        if (!$task->isTimelined()) {
            $TeKe->add_system_message(_("This task is not present on timeline. Removal not possible."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($task->removeFromTimeline()) {
            $response->setStateSuccess();
            $TeKe->add_system_message(_("Task removed from timeline."));
        } else {
            $TeKe->add_system_message(_("Task could not be removed from timeline."), 'error');
        }

        $response->setMessages();
        echo $response->getJSON();
        exit;
    }
