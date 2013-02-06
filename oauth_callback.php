<?php
require_once 'config.php';

session_start();

$token_url = LOGIN_URI . "/services/oauth2/token";

$code = $_GET['code'];

if (!isset($code) || $code == "") {
    die("Error - code parameter missing from request!");
}

$params = "code=" . $code
    . "&grant_type=authorization_code"
    . "&client_id=" . CLIENT_ID
    . "&client_secret=" . CLIENT_SECRET
    . "&redirect_uri=" . urlencode(REDIRECT_URI);

$curl = curl_init($token_url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ( $status != 200 ) {
    die("Error: call to token URL $token_url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
}

curl_close($curl);

$response = json_decode($json_response, true);

$access_token = $response['access_token'];
$instance_url = $response['instance_url'];

if (!isset($access_token) || $access_token == "") {
    die("Error - access token missing from response!");
}

if (!isset($instance_url) || $instance_url == "") {
    die("Error - instance URL missing from response!");
}

$_SESSION['access_token'] = $access_token;
$_SESSION['instance_url'] = $instance_url;

header( 'Location: demo_rest.php' ) ;
?>
