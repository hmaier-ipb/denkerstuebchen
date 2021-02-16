<?php
namespace control_denkerstuebchen_db;

require("include/classes/db.class.php");
use db_denkerstuebchen\db;
class control_db extends db
{
  function __construct(){
    $this->host = "127.0.0.1";
    $this->user = "www-data";
    $this->pwd = "";
    $this->db_name = "denkerstuebchen";
    
  }



  function errl($var){
    return error_log(json_encode($var));
  }


  function new_reservation(){
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $room_number = $_POST["room_number"];

      if($this->check_value($email,"user",$email)==0){
       error_log(json_encode("User is unknown"));
      }


  }
}