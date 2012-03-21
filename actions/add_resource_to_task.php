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

        $resource = new Resource(get_input('resource_id'));
        if (!$resource->getId()) {
            $TeKe->add_system_message(_("No such resource."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($task->isAssociatedResource($resource->getId())) {
            $TeKe->add_system_message(_("Resource is already associated with this task."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($task->addAssociatedResource($resource)) {
            $response->setStateSuccess();
            $TeKe->add_system_message(sprintf(_("%s added to task %s."), $resource->getTitle(), $task->getTitle()));
            $response->setMessages();
        } else {
            $TeKe->add_system_message(_("Resource could not be associated with task."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
