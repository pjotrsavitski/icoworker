<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $project = ProjectManager::getProjectById(get_input('project_id'));

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }

        // TODO Probably some general class could be needed for that
        $data = array('milestones' => array());

        $milestones = ProjectManager::getProjectMilestones($project->getId());
        if ($milestones && is_array($milestones) && sizeof($milestones) > 0) {
            foreach ($milestones as $milestone) {
                $data['milestones'][] = array('id' => $milestone->getId(), 'title' => $milestone->getTitle(), 'milestone_date' => $milestone->getMilestoneDate());
            }
        }

        echo json_encode($data);
        exit;
    }

    header('HTTP/1.0 404 Not Found');
    exit;
