<?php
// My session start function support timestamp management
function my_session_start($cid = ""): void {
    $session_lifetime = 60 * 60 * 24 * 7; // User should stay logged in for a week
    session_set_cookie_params([
        'lifetime' => $session_lifetime,
        'path' => '/',
        'secure' => true, // HTTPS
        'httponly' => true, // Recommended for better security
    ]);
    session_start();
    if ($cid !== "") $_SESSION['cid'] = $cid;
    if (!empty($_SESSION['set_timer']) && $_SESSION['set_timer'] < time() - $session_lifetime) {
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
<script type="application/javascript">
    const userCID = "<?php echo $_SESSION['cid'] ?? ""; ?>";
</script>
