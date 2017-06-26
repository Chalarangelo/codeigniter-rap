<?php
class Api extends CI_Controller {

  private $users, $auth;

  public function __construct(){
    parent::__construct();
    $this->load->helper('database');
    $this->load->helper('jwt');
    $this->load->helper('auth');
    $this->load->helper('rest_api');
    $this->users = array(
      "table" => "`users`",
      "create_fields" => ["`username`", "`password`", "`email`"],
      "create_types" => "sss",
      "read_fields" => "`username`, `user_id` AS `id`",
      "read_key" => "`username`"
    );
    $this->auth = array(
      "table" => "`users`",
      "fields" => "`username`, `user_id` AS `id`, `password`",
      "username_field" => "`username`",
      "password_field" => "`password`",
      "id_field" => "`id`",
      "service_name" => "pyramids.social",
      "cookie_name" => "pyramids_social_token"
    );
  }

  public function login(){
    header('Content-Type: application/json');
    if($this->input->method(true) != 'POST'){
      echo json_encode(array("message:"=>"Use the HTTP POST method to login to the system."));
      return;
    }
    else
      if(check_jwt_cookie($this->auth["service_name"], $this->auth["cookie_name"])){
        echo json_encode(regenerate_jwt_cookie($this->auth["service_name"], $this->auth["cookie_name"]));
        return;
      }
      else {
        echo json_encode(authorize($this->auth["table"], $this->auth["fields"],
          $this->auth["username_field"], $this->auth["password_field"], $this->auth["id_field"],
          $_POST["username"], hash("sha512", $_POST["password"], true),
          $this->auth["service_name"], $this->auth["cookie_name"]
        ));
        return;
      }
  }

  /**
   * Method for getting `user` resources.
   * Parameters:
   * @param $param - (Optional) The parameter used to uniquely identify the
   *  specific resource. If nothing is specified, all 'user' resoures will be
   *  listed.
   */
  public function users($param=''){
    header('Content-Type: application/json');
    if($this->input->method(true) == 'POST'){
      echo json_encode(createResourceElement(
        $this->users["table"], $this->users["create_fields"], $this->users["create_types"],
        [$_POST["username"],
        password_hash(hash("sha512", $_POST["password"], true), PASSWORD_DEFAULT),
        $_POST["email"]]
      ));
      return;
    }
    if($this->input->method(true) == 'PUT'){
      echo json_encode(updateResourceElement(
        $this->users["table"], ["password","email"], "ss", ["$2y$10\$cRnaeK79/TOcaAeFLmEuB.BdBIn1FYfNdq1dPkdJ1CuqOJcheUH0O", "dummy@gmail.com"], $this->users["read_key"], "dummy"
      ));
    }
    if($param == ''){
      echo json_encode(readResourceRoot( $this->users["table"], $this->users["read_fields"]));
      return;
    }
    else{
      $data = readResourceElement(
          $this->users["table"], $this->users["read_fields"], $this->users["read_key"],
          $param
      );
      if($data == false)
        echo(json_encode(array('message'=>'No user with this username.')));
      else
        echo json_encode($data[0]);
    }
  }

  /**
   * Index method for completeness. Returns a JSON response with an
   * error message.
   */
  public function index(){
    header('Content-Type: application/json');
    echo(json_encode(array('message'=>'No resource specified.')));
  }
}
?>
