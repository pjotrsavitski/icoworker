<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $comment = ProjectManager::getCommentById(get_input('comment_id'));

        if (!($comment instanceof Comment)) {
            $TeKe->add_system_message(_("No such comment."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        $project = $comment->getProjectObject();

        // TODO Possibly admin should be allowed to see stuff
        if (!(($project instanceof Project) && ($project->isMember(get_logged_in_user_id())))) {
            $TeKe->add_system_message(_("No project or insufficient privileges."), 'error');
            $response->setMessages();
            echo $response->getJSON();
            exit;
        }

        if ($comment->delete()) {
            $response->setStateSuccess();
            $TeKe->add_system_message(_("Comment deleted."));
        } else {
            $TeKe->add_system_message(_("Comment could not be deleted."), 'error');
        }

        $response->setMessages();
        echo $response->getJSON();
        exit;
    }
