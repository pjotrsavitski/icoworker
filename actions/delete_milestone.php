<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $milestone = ProjectManager::getMilestoneById(get_input('milestone_id'));

        if (!($milestone instanceof Milestone)) {
            $TeKe->add_system_message(_("No such milestone."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $milestone->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($milestone->delete()) {
            $response->setStateSuccess();
            $TeKe->add_system_message(_("Milestone deleted."));
        } else {
            $TeKe->add_system_message(_("Milestone could not be deleted."), 'error');
        }

        $response->setMessages();
        echo $response->getJSON();
        exit;
    }
