<?php
  require __DIR__.'/../../db.php';
  class Users extends CI_Controller {

    public function _remap($param){
      $this->index($param);
    }

    public function index($param){
      header('Content-Type: application/json');
      if($param=='index'){  // This is what it's empty looks like by default
        $data = $this->readAll();
        echo json_encode($data);
      }
      else {
        $data = $this->read($param);
        if($data == false)
          echo(json_encode(array('message'=>'No user with this username.')));
        else
          echo json_encode($data[0]);
      }
    }

    private function create($value=''){
      # code...
    }

    private function readAll(){
      return database_no_args_query(
        "SELECT username, user_id AS id
        FROM users"
      );
    }

    private function read($username){
      if(empty($username))
        return array("message"=>"The specified username was empty.");
      return database_query(
        "SELECT username, user_id AS id
        FROM users
        WHERE username=?",
        "s", [$username]
      );
    }

    private function update($value=''){
      # code...
    }

    private function delete($value=''){
      # code...
    }

  }
?>
