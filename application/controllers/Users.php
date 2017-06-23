<?php
  require __DIR__.'/../../db.php';
  class Users extends CI_Controller {

    public function _remap($param){
      $this->index($param);
    }

    public function index($param){
      //echo 'Hello world';
      //$r = $this->read('1337');
      $r = database_query("SELECT user_id, username FROM users WHERE user_id=? AND username=?", "ss", ["1337","dummy"]);
      // if ($r === false)
      //   { echo 'nope'; return;}
      header('Content-Type: application/json');
      echo json_encode($r[0]);
      //echo $r[0];
    }

    private function create($value='')
    {
      # code...
    }

    private function read($value="1337")
    {
      $connection = database_connect();
      // $d = "dummy";
      // $v = array("1137","dummy");
      // $refs = array();
      //   foreach($v as $key => $value)
      //       $refs[$key] = &$v[$key];
      // $type ="ss";
      $query = mysqli_prepare($connection, "SELECT user_id, username FROM users WHERE user_id=? AND username=?");
      //mysqli_stmt_bind_param($query,"ss", $v);
      call_user_func_array("mysqli_stmt_bind_param",array($query,"ss","1337","dummy"));
      // call_user_func_array(array($query,"bind_param"),$refs);
      mysqli_stmt_execute($query);
      $result = mysqli_stmt_get_result($query);
      //mysqli_stmt_bind_result($query, $table);
      $rows = array();

      if ($result === false)
        return false;
      while ($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
        // printf("%s %s\n", $row['user_id'],$row['username']);
      }
      /*
      // if (mysqli_stmt_num_rows($query) == 0)  return false;

      //while ($row = mysqli_fetch_assoc($result))
      //  $rows[] = $row;

      while (mysqli_stmt_fetch($query)) {
        $rows[] = array('id' => $table['user_id'], 'username' => $table['username']);
        printf("%s %s\n",  $table['user_id'],  $table['username']);
      }
      */
      return $rows;
    }

    private function update($value='')
    {
      # code...
    }

    private function delete($value='')
    {
      # code...
    }

  }
?>
