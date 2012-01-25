<?php
class Image {
    public $id;
    public $name;
    public $type;
    public $size;
    public $location;
    public $creator;
    public $created;
    public $width = 0;
    public $height = 0;
    public $image;
    public $image_type;
    public $file;
    private $locked = 0;
    
    function __construct($id=false) {
        if ($id && is_numeric($id)) {
            $this->id=$id;
            $this->load();
        }
    }
    
    function view($thumb = false) {
        $add = "";
        if ($thumb) $add = "&thumb=1";
        return WWW_ROOT . "images/image.php?id=".$this->id.$add;
    }
    
    function load() {
        if (is_numeric($this->id)) {
            $data = query_row("SELECT * FROM " . DB_PREFIX . "images WHERE id=".$this->id);
            if ($data) {
                $this->name = $data->name;
                $this->type = $data->type;
                $this->size = $data->size;
                $this->height = $data->height;
                $this->width = $data->width;
                $this->location = $data->location;
                $this->creator = $data->creator;
                $this->created = $data->created;
                $this->setLocked($data->locked);
            } else {
                $this->id = 0;    
            }
        }
    }
    
    function delete() {
        if (is_admin() || $this->creator==get_logged_in_user()->getId()) {
            $deleted = query("DELETE FROM " . DB_PREFIX . "images WHERE id=".$this->id);
            if ($deleted) {
                $imgs = query_rows("SELECT id FROM " . DB_PREFIX . "images WHERE name='".$this->name."' AND size=".$this->size);
                if (!$imgs){
                    unlink(DATA_STORE . $this->name);
                    return true;
                }
                return true;
            }
        }
        return false;
    }
    
    function rename() {
        preg_match("/(.*)\.(jpeg|gif|jpg|pjpeg)/", $this->name, $name);
        $count = 1;
        while (file_exists(DATA_STORE . $name[1] . "_" . $count . "." . $name[2])) {
            $count++;
        }
        $this->name = $name[1] . "_" . $count . "." . $name[2];
    }
    
    function resizeToHeight($height) {
        $ratio = $height / $this->height;
        $width = $this->width * $ratio;
        return $this->resize($width,$height);
    }
       
    function resizeToWidth($width) {
        $ratio = $width / $this->width;
        $height = $this->height * $ratio;
        return $this->resize($width,$height);
    }
    
    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        if ($this->image_type == IMAGETYPE_GIF) {
            $new_image = imagecreate($width, $height);
        }
        if (($this->image_type == IMAGETYPE_GIF) OR ($this->image_type==IMAGETYPE_PNG)){
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
        }
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        return $new_image;
    }
    
    function copy_it() {
        return $this->resize($this->width, $this->height);
    }
    
    function save_image($image_file, $filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
        if( $image_type == IMAGETYPE_JPEG ) {
           imagejpeg($image_file,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {
           imagegif($image_file,$filename);         
        } elseif( $image_type == IMAGETYPE_PNG ) {
           imagepng($image_file,$filename);
        }   
        if( $permissions != null) {
           chmod($filename,$permissions);
        }
    }

    
    function thumbnail($size = 100) {
        preg_match("/(.*)\.(jpeg|gif|jpg|pjpeg|png)/", $this->name, $name);
        $filename = DATA_STORE . $name[1] . "_thumbnail" . "." . $name[2];
        if ($this->width > $size && $this->width >= $this->height) {
            $this->save_image($this->resizeToWidth($size), $filename, $this->image_type);
        } else if ($this->height > $size && $this->height > $this->width){
            $this->save_image($this->resizeToHeight($size), $filename, $this->image_type);
        } else {
            $this->save_image($this->copy_it(), $filename, $this->image_type);
        }
    }
    
    function save_from_dir($dir, $name) {
        $this->file = $dir."/".$name;
        $this->name = strtolower($name);
        $info = getimagesize($this->file);
        $this->type = image_type_to_mime_type($info[2]);
        $this->size = filesize($this->file);
        return $this->save();
}
    
    function upload($file) {
        $allowed_types = array("image/gif", "image/jpeg", "image/pjpeg", "image/png");    
        if (in_array($file["type"], $allowed_types) && $file["size"] < 25000000) {   
            if ($file["error"] > 0) {
                return false;
            } else {
                $this->file = $file["tmp_name"];
                $this->name = strtolower($file["name"]);
                $this->type = $file["type"];
                $this->size = $file["size"];
                return $this->save();
            }
        } else {
            global $TeKe;
            $TeKe->add_system_message(_("Invalid image. Type: ").$file["type"], 'error');
            return false;
        }
    }
    
    function copy($locked=false) {
        $this->creator = get_logged_in_user()->getId();
        $insert = query_insert("INSERT INTO " . DB_PREFIX . "images (name, type, size, location, creator, width, height) values ('".mysql_real_escape_string($this->name)."', '".mysql_real_escape_string($this->type)."', ".$this->size.", '".mysql_real_escape_string($this->location)."', ".$this->creator.", ".$this->width.", ".$this->height.")");
        if ($insert && $locked) {
            $lock = $this->lock($insert);
        }
        return $insert;
    }
    
    function lock($id=null) {
        if (!$id) $id = $this->getId();
        return query_update("UPDATE " . DB_PREFIX . "images SET locked=1 WHERE id=".$id);
    }

    function save() {
        $new_file = true;
        if (file_exists(DATA_STORE . $this->name)) {
            if ($this->size == filesize(DATA_STORE . $this->name)) {
                $this->location = DATA_STORE . $this->name;
                $new_file = false;
            } else {
                $this->rename();
            }
        }
        if ($new_file){
            if (!move_uploaded_file($this->file, DATA_STORE . $this->name)) {
                copy($this->file, DATA_STORE . $this->name);
            }
            $this->location = DATA_STORE . $this->name;
        }
        $filename = DATA_STORE . $this->name;
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {
            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {
            $this->image = imagecreatefromgif($filename);
        } elseif( $this->image_type == IMAGETYPE_PNG ) {
            $this->image = imagecreatefrompng($filename);
        }
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
        if ($new_file){
            $this->thumbnail();
        }
        if ($this->id > 0) {
            if (query_update("UPDATE " . DB_PREFIX . "images SET name='".mysql_real_escape_string($this->name)."', type='".mysql_real_escape_string($this->type)."', size=".$this->size.", location='".mysql_real_escape_string($this->location)."', width=".$this->width.", height=".$this->height." WHERE id=".$this->id)) return $this->id;
        } else {
            $this->creator = get_logged_in_user()->getId();
            $insert = query_insert("INSERT INTO " . DB_PREFIX . "images (name, type, size, location, creator, width, height) values ('".mysql_real_escape_string($this->name)."', '".mysql_real_escape_string($this->type)."', ".$this->size.", '".mysql_real_escape_string($this->location)."', ".$this->creator.", ".$this->width.", ".$this->height.")");
            return $insert;
        }
    }
    
    public function getLocked() {
        return $this->locked;
    }
    
    public function setLocked($locked) {
        if ($locked) {
            $this->locked = 1;
        } else {
            $this->locked = 0;
        }
    }
       
}
?>
