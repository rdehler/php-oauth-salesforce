<?php
require_once 'config.php';
$auth_url = LOGIN_URI
	. "/services/oauth2/authorize?response_type=code&client_id="
	. CLIENT_ID . "&redirect_uri=" . urlencode(REDIRECT_URI);
header('Location: ' . $auth_url);
?>
