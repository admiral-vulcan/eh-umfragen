<?php
// use sessions
session_start();
// include google API client
require_once "assets/google/vendor/autoload.php";

// set google client ID
$google_oauth_client_id = "***REMOVED***";

// create google client object with client ID
$client = new Google_Client([
    'client_id' => $google_oauth_client_id
]);

// verify the token sent from AJAX
$id_token = $_POST["id_token"];

$payload = $client->verifyIdToken($id_token);
if ($payload && $payload['aud'] == $google_oauth_client_id)
{
    // get user information from Google & login the user
    $_SESSION["g_id"] = $payload["sub"];
    $_SESSION["g_name"] = $payload["name"];
    $_SESSION["g_given_name"] = $payload["given_name"];
    $_SESSION["g_family_name"] = $payload["family_name"];
    $_SESSION["g_email"] = $payload["email"];
    $_SESSION["g_email_verified"] = $payload["email_verified"];
    $_SESSION["g_picture"] = $payload["picture"];
    $_SESSION["g_locale"] = $payload["locale"];

    // send the response back to client side
}
else
{
    // token is not verified or expired
    echo "Failed to login.";
}
?>