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
        $data = array('beginning' => format_date_for_js($project->getStartDate()), 'end' => format_date_for_js($project->getEndDate()), 'milestones' => array(), 'documents' => array(), 'tasks' => array());

        $milestones = ProjectManager::getProjectMilestones($project->getId());
        if ($milestones && is_array($milestones) && sizeof($milestones) > 0) {
            foreach ($milestones as $milestone) {
                $data['milestones'][] = array('id' => $milestone->getId(), 'title' => $milestone->getTitle(), 'milestone_date' => format_date_for_js($milestone->getMilestoneDate()), 'flag_url' => $milestone->getFlagColorURL(), 'notes' => $milestone->getNotes());
            }
        }

        $documents = ProjectManager::getProjectDocuments($project->getId());
        if ($documents && is_array($documents) && sizeof($documents) > 0) {
            foreach($documents as $document) {
                $data['documents'][] = array('id' => $document->getId(), 'title' => $document->getTitle(), 'url' => $document->getUrl(), 'created' => format_date_for_js($document->getCreated()), 'is_active' => $document->isActive(), 'end_date' => format_date_for_js($document->getEndDate()), 'versions' => $document->getVersions());
            }
        }

        $comments = ProjectManager::getProjectComments($project->getId());
        if ($comments && is_array($comments) && sizeof($comments) > 0) {
            foreach($comments as $comment) {
                $data['comments'][] = array('id' => $comment->getId(), 'content' => $comment->getContent(), 'comment_date' => format_date_for_js($comment->getCommentDate()));
            }
        }

        $tasks = ProjectManager::getProjectTimelinedTasks($project->getId());
        if ($tasks && is_array($tasks) && sizeof($tasks) > 0) {
            foreach ($tasks as $task) {
                $task_data = array(
                    'id' => $task->getId(),
                    'title' => $task->getTitle(),
                    'description' => $task->getDescription(),
                    'members' => array(),
                    'resources' => array(),
                    'start_date' => format_date_for_js($task->getStartDate()),
                    'end_date' => format_date_for_js($task->getEndDate())
                );
                $task_members = $task->getAssociatedMembers();
                if ($task_members && is_array($task_members) && sizeof($task_members) > 0) {
                    foreach ($task_members as $tmember) {
                        $task_data['members'][] = array(
                            'id' => $tmember->getId(),
                            'url' => $tmember->getURL(),
                            'fullname' => $tmember->getFullname(),
                            'image_url' => $tmember->getImageURL()
                        );
                    }
                }
                $task_resources = $task->getAssociatedResources();
                if ($task_resources && is_array($task_resources) && sizeof($task_resources) > 0) {
                    foreach ($task_resources as $tres) {
                        $task_data['resources'][] = array(
                            'id' => $tres->getId(),
                            'title' => $tres->getTitle(),
                            'resource_type_url' => $tres->getResourceTypeURL(),
                            'url' => $tres->getURL(),
                            'description' => $tres->getDescription()
                        );
                    }
                }
                $data['tasks'][] = $task_data;
            }
        }

        echo json_encode($data);
        exit;
    }

    header('HTTP/1.0 404 Not Found');
    exit;
