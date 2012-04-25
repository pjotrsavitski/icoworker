<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $project = ProjectManager::getProjectById(get_input('project_id'));

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            forward("");
            exit;
        }

        $user = get_logged_in_user();

        if (!$project->isMember($user->getId())) {
            $TeKe->add_system_message(_("Not a participant."), 'error');
            forward("");
            exit;
        }

        if ($project->getCreator() == $user->getId()) {
            $TeKe->add_system_message(_("Owner can not leave the project."), 'error');
            forward("");
            exit;
        }

        if ($project->removeMember($user->getId())) {
            $TeKe->add_system_message(sprintf(_("You have left the project %s."), $project->getTitle()));
        } else {
            $TeKe->add_system_message(_("Participant could not be removed."), 'error');
        }

        forward("");
        exit;
    }
