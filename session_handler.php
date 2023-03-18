<?php
// My session start function support timestamp management
function my_session_start($cid = ""): void {
    //ini_set('session.use_strict_mode', 0);
    session_start();
    if ($cid !== "") $_SESSION['cid'] = $cid;
    if (!empty($_SESSION['set_timer']) && $cid == "") $_SESSION['set_timer'] = time();

    // Do not allow to use too old session ID
    if (!empty($_SESSION['set_timer']) && $_SESSION['set_timer'] < time() - 60 * 60 * 24 * 7) {  //User stays logged in for a week
        session_destroy();
        session_start();
    }
    $_SESSION['set_timer'] = time(); //set and reset time
}

function logout(): void {
    $_SESSION['cid'] = null;
    $_SESSION['set_timer'] = null;
    $_SESSION[] = array();
    session_destroy();
}
?>