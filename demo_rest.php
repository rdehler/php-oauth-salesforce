<?php
session_start();

function show_accounts($instance_url, $access_token) {
    $query = "SELECT Name, Id from Account LIMIT 100";
    $url = "$instance_url/services/data/v20.0/query?q=" . urlencode($query);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token"));

    $json_response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($json_response, true);

    $total_size = $response['totalSize'];

    echo "$total_size record(s) returned<br/><br/>";
    foreach ((array) $response['records'] as $record) {
        echo $record['Id'] . ", " . $record['Name'] . "<br/>";
    }
    echo "<br/>";
}

function create_account($name, $instance_url, $access_token) {
    $url = "$instance_url/services/data/v20.0/sobjects/Account/";

    $content = json_encode(array("Name" => $name));

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token",
                "Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 201 ) {
        die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }
    
    echo "HTTP status $status creating account<br/><br/>";

    curl_close($curl);

    $response = json_decode($json_response, true);

    $id = $response["id"];

    echo "New record id $id<br/><br/>";

    return $id;
}

function show_account($id, $instance_url, $access_token) {
    $url = "$instance_url/services/data/v20.0/sobjects/Account/$id";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token"));

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 200 ) {
        die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }

    echo "HTTP status $status reading account<br/><br/>";

    curl_close($curl);

    $response = json_decode($json_response, true);

    foreach ((array) $response as $key => $value) {
        echo "$key:$value<br/>";
    }
    echo "<br/>";
}

function update_account($id, $new_name, $city, $instance_url, $access_token) {
    $url = "$instance_url/services/data/v20.0/sobjects/Account/$id";

    $content = json_encode(array("Name" => $new_name, "BillingCity" => $city));

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token",
                "Content-type: application/json"));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 204 ) {
        die("Error: call to URL $url failed with status $status, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }

    echo "HTTP status $status updating account<br/><br/>";

    curl_close($curl);
}

function delete_account($id, $instance_url, $access_token) {
    $url = "$instance_url/services/data/v20.0/sobjects/Account/$id";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Authorization: OAuth $access_token"));
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

    curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ( $status != 204 ) {
        die("Error: call to URL $url failed with status $status, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
    }

    echo "HTTP status $status deleting account<br/><br/>";

    curl_close($curl);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>REST/OAuth Example</title>
    </head>
    <body>
        <tt>
            <?php
            $access_token = $_SESSION['access_token'];
            $instance_url = $_SESSION['instance_url'];

            if (!isset($access_token) || $access_token == "") {
                die("Error - access token missing from session!");
            }

            if (!isset($instance_url) || $instance_url == "") {
                die("Error - instance URL missing from session!");
            }

            show_accounts($instance_url, $access_token);

            $id = create_account("My New Org", $instance_url, $access_token);

            show_account($id, $instance_url, $access_token);

            show_accounts($instance_url, $access_token);

            update_account($id, "My New Org, Inc", "San Francisco",
                    $instance_url, $access_token);

            show_account($id, $instance_url, $access_token);

            show_accounts($instance_url, $access_token);

            delete_account($id, $instance_url, $access_token);

            show_accounts($instance_url, $access_token);
            ?>
        </tt>
    </body>
</html>
