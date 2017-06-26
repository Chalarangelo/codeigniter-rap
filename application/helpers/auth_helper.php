<?php
function authorize($table, $fields, $username_field, $password_field,
  $id_field, $username_value, $password_value, $service_name, $cookie_name){
  // Load the database helper for querying
  $ci =& get_instance(); $ci->load->helper('database');
  $password_field = str_replace("`", "", $password_field);
  $id_field = str_replace("`", "", $id_field);
  $user = database_query(
    "SELECT ".$fields." FROM ".$table." WHERE ".$username_field."=?",
    "s", [$username_value]
  );
  if($user == false)
    return array("message"=>"No user with this username.");

  if(password_verify($password_value, $user[0][$password_field])){
    generate_jwt_cookie($username_value, $user[0][$id_field],  $service_name, $cookie_name);
    unset($user[0][$password_field]);
    return $user[0];
  }
  else
    return array("message"=>"Password is incorrect.");
}

function generate_jwt_cookie($username_value, $id_value, $service_name, $cookie_name){
  // Load the database helper for querying
  $ci =& get_instance(); $ci->load->helper('database');
  $secret = parse_ini_file(__DIR__.'/../../config.ini')["secret"];

  $timestamp = date_timestamp_get(date_create());
  mt_srand(intval(substr($timestamp,-16,12)/substr(join(array_map(function ($n) { return sprintf('%03d', $n); }, unpack('C*', $secret))),0,2)));
  $stamp_validator = mt_rand();

  $token = array(
    "iat" => $timestamp,
    "chk" => $stamp_validator,
    "username" => $username_value,
    "id" => $id_value,
    "iss" => $service_name
  );
  $cookie = array (
    "id" => $id_value,
    "token" => jwt_encode($token, $secret)
  );
  // Change the first NULL below to set a domain, change the second NULL below
  // to make this only transmit over HTTPS
  setcookie($cookie_name, json_encode($cookie), 0, "/", NULL, NULL, false );
}

function regenerate_jwt_cookie($service_name, $cookie_name){
  // Load the database helper for querying
  $ci =& get_instance(); $ci->load->helper('jwt');
  $secret = parse_ini_file(__DIR__.'/../../config.ini')["secret"];

  if(!isset($_COOKIE[$cookie_name]))
    return array("message"=>"Token not found.");

  $cookie_contents = json_decode($_COOKIE[$cookie_name], true);
  $token = (array)jwt_decode($cookie_contents["token"], $secret);

  generate_jwt_cookie($token["username"], $token["id"], $service_name, $cookie_name);
  return array("message"=>"Token regenerated successfully.");
}

function check_jwt_cookie($service_name, $cookie_name){
  // Load the database helper for querying
  $ci =& get_instance(); $ci->load->helper('jwt');
  $secret = parse_ini_file(__DIR__.'/../../config.ini')["secret"];

  if(!isset($_COOKIE[$cookie_name]))
    return false;

  $cookie_contents = json_decode($_COOKIE[$cookie_name], true);
  $token = (array)jwt_decode($cookie_contents["token"], $secret);

  if($token["iss"] != $service_name)
    return false;

  if($token["id"] != $cookie_contents["id"])
    return false;

  mt_srand(intval(substr($token["iat"],-16,12)/substr(join(array_map(function ($n) { return sprintf('%03d', $n); }, unpack('C*', $secret))),0,2)));
  $stamp_validator = mt_rand();
  if($stamp_validator != $token["chk"])
    return false;

  return true;
}


?>
