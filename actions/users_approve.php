<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $ids = get_input('user');
    if (!is_array($ids)) $ids = array($ids);

    if ($TeKe->is_admin()) {
        foreach ($ids as $id) { 
            $TeKe->user->user_approve($id);
        }
    }

    forward("administrate/user");
?>

