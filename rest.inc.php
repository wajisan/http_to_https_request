<?php

/**
 * PHP Rest CURL
 * https://github.com/jmoraleda/php-rest-curl
 * (c) 2014 Jordi Moraleda
 */

Class RestCurl {
  public static function exec($method, $url, $obj = array()) {
     
    $curl = curl_init();
     
    switch($method) {
      case 'GET':
        if(strrpos($url, "?") === FALSE) {
          $url .= '?' . http_build_query($obj);
        }
        break;

      case 'POST': 
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");//
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($obj));
        break;

      case 'PUT':
      case 'DELETE':
      default:
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method)); // method
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($obj)); // body
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);//wait 6s max 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    
    $header_arr = getallheaders();
    $curr_header_arr = array('Accept:application/json', 'Content-Type:application/json');
    if (array_key_exists('Authorization', $header_arr)) {
      $bearer = "Authorization:".$header_arr["Authorization"];
      $curr_header_arr = array('Accept:application/json', 'Content-Type:application/json', $bearer);
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $curr_header_arr); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    
    // Exec
    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    //var_dump($info);
    curl_close($curl);
    
    // Data
    $header = trim(substr($response, 0, $info['header_size']));
    $body = substr($response, $info['header_size']);
     
    //var_dump($header);
    return array(/*'status' => $info['http_code'], 'header' => $header, */'data' => json_decode($body));
  }

  public static function get($url, $obj = array()) {
     return RestCurl::exec("GET", $url, $obj);
  }

  public static function post($url, $obj = array()) {
     return RestCurl::exec("POST", $url, $obj);
  }

  public static function put($url, $obj = array()) {
     return RestCurl::exec("PUT", $url, $obj);
  }

  public static function delete($url, $obj = array()) {
     return RestCurl::exec("DELETE", $url, $obj);
  }
}