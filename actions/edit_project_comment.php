<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    global $TeKe;

    if ($TeKe->is_logged_in() && $TeKe->has_access(ACCESS_CAN_EDIT)) {
        $response = new AJAXResponse();
        $comment = ProjectManager::getCommentById(get_input('comment_id'));

        if(!($comment instanceof Comment)) {
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

        // Define fields array
        $fields = array('content' => true, 'comment_date' => true);
        $inputs = array();

        foreach ($fields as $key => $requirement) {
            $inputs[$key] = get_input($key);
            if ($requirement && !$inputs[$key]) {
                $response->addError($key);
            }
            if ($key == 'comment_date') {
                $inputs[$key] = strtotime($inputs[$key]);
                // Check if date chosen fits into the project time frame
                if (!($inputs[$key] >= strtotime($project->getStartDate()) && $inputs[$key] <= strtotime($project->getEndDate()))) {
                    $response->addError($key);
                }
            }
            if (in_array($key, array('content'))) {
                $inputs[$key] = force_plaintext($inputs[$key]);
            }
        }

        if (sizeof($response->getErrors()) == 0) {
            if (Comment::update($comment, $inputs['content'], $inputs['comment_date'])) {
                // Get a fresh compy of modified comment
                $comment = new Comment($comment->getId());
                $response->addData('id', $comment->getId());
                $response->addData('content', $comment->getContent());
                $response->setStateSuccess();
                $TeKe->add_system_message(_("Comment changed."));
                $response->setMessages();
            }
        } else {
            $TeKe->add_system_message(_("Comment could not be changed."), 'error');
            $response->setMessages();
        }

        echo $response->getJSON();
        exit;
    }
