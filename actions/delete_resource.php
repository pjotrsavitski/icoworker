<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $resource = ProjectManager::getResourceById(get_input('resource_id'));

        if (!($resource instanceof Resource)) {
            $TeKe->add_system_message(_("No such resource."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $resource->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($resource->delete()) {
            $response->setStateSuccess();
            $TeKe->add_system_message(_("Resource deleted."));
        } else {
            $TeKe->add_system_message(_("Resource could not be deleted."), 'error');
        }

        $response->setMessages();
        echo $response->getJSON();
        exit;
    }
