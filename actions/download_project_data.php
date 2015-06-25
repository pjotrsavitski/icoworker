<?php

require_once(dirname(dirname(__FILE__))."/engine/engine.php");

global $TeKe;

if (!($TeKe->is_logged_in() && $TeKe->is_admin())) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
    exit;
}    

$project = ProjectManager::getProjectById(get_input('project_id'));

if (!($project instanceof Project)) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    exit;
}

$members = $project->getMembers();
$tasks = ProjectManager::getProjectTasks($project->id);
$resources = ProjectManager::getProjectResources($project->id);
$milestones = ProjectManager::getProjectMilestones($project->id);
$documents = ProjectManager::getProjectDocuments($project->id);
$comments = ProjectManager::getProjectComments($project->id);

$project_data = [];
$project_data['id'] = (int)$project->id;
$project_data['creator'] = (int)$project->getCreator();
$project_data['title'] = $project->getTitle();
$project_data['description'] = $project->getGoal();
$project_data['start_date'] = format_date_for_js($project->getStartDate());
$project_data['end_date'] = format_date_for_js($project->getEndDate());
$project_data['created'] = format_date_for_js($project->getCreated());
$project_data['updated'] = format_date_for_js($project->getUpdated());
$project_data['members'] = [];
$project_data['tasks'] = [];
$project_data['resources'] = [];
$project_data['milestones'] = [];
$project_data['documents'] = [];
$project_data['annotations'] = [];

if ($members && is_array($members)) {
    foreach($members as $member) {
        $project_data['members'][] = [
            'id' => (int)$member->id,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'email' => $member->email,
            'image_url' => $member->getImageURL('square'),
        ];
    }
}

if ($tasks && is_array($tasks)) {
    foreach($tasks as $task) {
        $associated_members = [];
        $associated_resources = [];
        $t_a_m = $task->getAssociatedMembers();
        $t_a_r = $task->getAssociatedResources();

        if ($t_a_m && is_array($t_a_m)) {
            foreach($t_a_m as $single) {
                $associated_members[] = (int)$single->id;
            }
        }

        if ($t_a_r && is_array($t_a_r)) {
            foreach($t_a_r as $single) {
                $associated_resources[] = (int)$single->id;
            }
        }

        $project_data['tasks'][] = [
            'id' => (int)$task->id,
            'creator' => (int)$task->creator,
            'title' => $task->title,
            'description' => $task->description,
            'start_date' => format_date_for_js($task->start_date),
            'end_date' => format_date_for_js($task->end_date),
            'created' => format_date_for_js($task->created),
            'updated' => format_date_for_js($task->updated),
            'is_timelined' => (bool)$task->is_timelined,
            'members' => $associated_members,
            'resources' => $associated_resources,
        ];
    }
}

if ($resources && is_array($resources)) {
    foreach($resources as $resource) {
        $project_data['resources'][] = [
            'id' => (int)$resource->id,
            'creator' => (int)$resource->creator,
            'title' => $resource->title,
            'description' => $resource->description,
            'url' => $resource->url,
            'resource_type' => (int)$resource->resource_type, // default (url), text, spreadsheet, presentation
            'created' => format_date_for_js($resource->created),
            'updated' => format_date_for_js($resource->updated),
        ];
    }
}

if ($milestones && is_array($milestones)) {
    foreach($milestones as $milestone) {
        $project_data['milestones'][] = [
            'id' => (int)$milestone->id,
            'creator' => (int)$milestone->creator,
            'title' => $milestone->title,
            'description' => $milestone->notes,
            'date' => format_date_for_js($milestone->milestone_date),
            'color' => (int)$milestone->flag_color, // red, violet, green, blue, orange, black
            'created' => format_date_for_js($milestone->created),
            'updated' => format_date_for_js($milestone->updated),
        ];
    }
}

if ($documents && is_array($documents)) {
    foreach($documents as $document) {
        $associated_versions = [];
        $d_v = $document->getVersions();

        if ($d_v && is_array($d_v)) {
            foreach($d_v as $single) {
                $associated_versions[] = [
                    'id' => (int)$single->id,
                    'creator' => (int)$single->creator,
                    'title' => $single->title,
                    'description' => $single->notes,
                    'url' => $single->url,
                    'created' => format_date_for_js($single->created),
                    'version_type' => (int)$single->version_type, // live, ready, dropped
                ];
            }
        }

        $project_data['documents'][] = [
            'id' => (int)$document->id,
            'creator' => (int)$document->creator,
            'title' => $document->title,
            'description' => $document->notes,
            'url' => $document->url,
            'created' => format_date_for_js($document->created),
            'updated' => format_date_for_js($document->updated),
            'is_active' => (bool)$document->is_active,
            'end_date' => format_date_for_js($document->end_date),
            'versions' => $associated_versions,
        ];
    }
}

if ($comments && is_array($comments)) {
    foreach($comments as $comment) {
        $project_data['annotations'][] = [
            'id' => (int)$comment->id,
            'creator' => (int)$comment->creator,
            'title' => $comment->content,
            'description' => $comment->content,
            'date' => format_date_for_js($comment->comment_date),
            'created' => format_date_for_js($comment->created),
            'updated' => format_date_for_js($comment->updated),
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($project_data, JSON_PRETTY_PRINT);
exit;
