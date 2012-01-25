<?php

require_once(dirname(dirname(__FILE__))."/engine/engine.php");

$image_id = get_input("id", false);
$thumb = get_input("thumb", false);
if (is_numeric($image_id)) {
    $image = new Image($image_id);
    $fn = $image->name;
    if ($thumb) { 
        preg_match("/(.*)\.(jpeg|gif|jpg|pjpeg|png)/", $fn, $name);
        $fn = $name[1] . "_thumbnail" . "." . $name[2];
    }
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");  
    header("Content-size: ".$image->size);
    header('Content-Disposition: filename='.$fn);
    header("Content-type: ".$image->type);
    readfile(DATA_STORE . $fn);
} else {
    return "";
}
?>
