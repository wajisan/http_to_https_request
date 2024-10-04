<?php
require_once('./rest.inc.php');
$curr_domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$curr_url = $curr_domain.$_SERVER[REQUEST_URI];

//var_dump(getallheaders());
function showData($data) {
    //echo '<pre style="word-wrap: break-word; white-space: pre-wrap;">';

    if (is_array($data)) {
        echo json_encode ((array) $data, JSON_PRETTY_PRINT);
    }
    else {
        echo $data;
    }
    //echo '</pre>';
}

function myPatch($url, $obj) {
    unset($obj["http_method"]);
    $ch = curl_init( $url );
    $payload = json_encode($obj);
    curl_setopt($ch, CURLOPT_PATCH, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $header_arr = getallheaders();
    $curr_header_arr = array('Content-Type:application/json');
    if (array_key_exists('Authorization', $header_arr)) {
      $bearer = "Authorization:".$header_arr["Authorization"];
      $curr_header_arr = array('Content-Type:application/json', $bearer);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $curr_header_arr); 
    # Return response instead of printing.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    # Print response.
    return $result;
    
}

function myPost($url, $obj) {
    $ch = curl_init( $url );
    $header_arr = getallheaders();
    
    if (array_key_exists('Authorization', $header_arr) && !empty($header_arr["Authorization"])) {
        //if authorization is not empty, we send JSON format
        $bearer = "Authorization:".$header_arr["Authorization"];
        $curr_header_arr = array('Content-Type:application/json', $bearer);
        $payload = json_encode($obj);
    }
    else {
        //if authorization is empty, we send form-urlencoded format
        $curr_header_arr = array('Content-Type:application/x-www-form-urlencoded');
        $payload = http_build_query($obj);
        $payload = utf8_encode($payload);
    }
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $curr_header_arr);
    //$payload = http_build_query($obj);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    # Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    # Print response.
    return $result;
}

if (!empty($_POST) && !empty($_POST["url_target"])) {
    $url = $_POST["url_target"];//need to check if correct url format 
    unset($_POST["url_target"]);
    //$url = $url_target;
    if (!empty($_POST["http_method"]) && $_POST["http_method"] == "PATCH") {
        $result = myPatch($url, $_POST);
    }
    else {
        $result = myPost($url, $_POST);
    }
    showData($result);
}
else if (!empty($_GET) && !empty($_GET["url_target"])) {
    $url_target = $_GET["url_target"];//need to check if correct url format 
    unset($_GET["url_target"]);
    $url = $url_target."?".http_build_query($_GET);

    $result = RestCurl::get($url);
    showData($result);
}

//https://faure-engineering.proxy.wajitech.fr/?url_target=https://isabelle33-dev-ed.my.salesforce.com/services/oauth2/token&username=nicolas@fe.com&password=Jxe45frm!&grant_type=password&client_id=3MVG9HxRZv05HarRuN0vTO5i.2GyJ0Xf3mdAfXcuO2r7CeNR5XkwR.YLD0z5u57ze52n86CVGxu1lseL.x9x0&client_secret=74202151ABCA3E8C188124C5B4F8A1E59D1A701F11B16CBD4DAA3731B4EC3962
//https://isabelle33-dev-ed.my.salesforce.com/services/oauth2/token?username=nicolas@fe.com&password=Jxe45frm!&grant_type=password&client_id=3MVG9HxRZv05HarRuN0vTO5i.2GyJ0Xf3mdAfXcuO2r7CeNR5XkwR.YLD0z5u57ze52n86CVGxu1lseL.x9x0&client_secret=74202151ABCA3E8C188124C5B4F8A1E59D1A701F11B16CBD4DAA3731B4EC3962
?>