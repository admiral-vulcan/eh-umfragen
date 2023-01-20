<?php
/**TODO
 * Get everything harddrive HERE!!
 *
 */

function getProfilePic() {
    if (isset($_SESSION['cid'])) {
        $filename = $_SESSION['cid'];
        // Search for file with the matching filename in /images/creatorPics
        foreach (glob("./images/creatorPics/{$filename}.*") as $file) {
            // Get the file extension
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            // Get the file path without the extension
            $path = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME);
            // If a file is found, return it along with the 'Profilbild' alt text and the file extension
            return array('path' => $path, 'ext' => $ext, 'alt' => 'Profilbild');
        }
    }
    // If no file is found or cid is not set, return the default image with the 'Ein Klemmbrett als Logo' alt text
    return array('path' => 'images/logo', 'ext' => 'png', 'alt' => 'Ein Klemmbrett als Logo');
}
?>
