<?php
    require_once(dirname(dirname(__FILE__))."/engine/engine.php");

    $ids = get_input("examinee");
    if (!is_array($ids)) $ids = array($ids);

    if (get_input("delete_button")) {
        foreach ($ids as $id) {
            $TeKe->examinee->load($id);
            $TeKe->examinee->delete();
        }
    }

    forward("examinees/view");
?>

