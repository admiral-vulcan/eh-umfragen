<?php
if (isset($_FILES["image"])) {
    $target_dir = "images/tmp/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
}
?>
