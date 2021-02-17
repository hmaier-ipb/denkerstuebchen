<?php
namespace control_denkerstuebchen_db;
require ("include/classes/easy_pdo_db.class.php");
use db_denkerstuebchen\pdo;

class control_db extends pdo\easy_pdo_db
{
  //MAIN FUNCTION
  function new_reservation()
  {
    $response = [];

    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $department = $_POST["department"];
    $status = $_POST["status"];

    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $room_number = $_POST["room_number"];


    $cols_user =  "firstname,surname,phone,email,department,status";
    $vars_array_user = [$name,$surname,$phone,$email,$department,$status];

    $user_id = $this->is_user_existing($email,$cols_user,$vars_array_user);
    $reservation_status = $this->has_user_reservations($user_id);

    if($reservation_status[0] !== 0){
      $response = $reservation_status;
    }else{
      $this->make_reservation($user_id,$start_date,$end_date,$room_number);
      $response = "success";
    }

    return $response;
  }

  function is_user_existing($email,$cols_user,$vars_array_user){
    if($this->does_exist("user","email",$email) == 0){
      $this->insert_into("user",$cols_user,$vars_array_user);
    }
    return $this->get_id("user","email",$email);
  }

  function has_user_reservations($user_id){
    $user_data = [];
    for($i=1;$i<=5;$i++){
      if($this->does_exist("tr_$i","user_id",$user_id) == 1){
        $user_data[] = "existing_reservation";
        $user_data[] = $this->get_row_by_id("tr_$i",$user_id);
        $user_data[] = $i;
        // user_data ARRAY = [id,[id,user_id,start_date,end_date],room_number]
        return $user_data; //already a reservation
      }
    }
    return [0];//no reservation
  }

  function make_reservation($user_id,$start_date,$end_date,$room_number){
    $cols_string = "user_id, start_date, end_date";
    $vars = [$user_id,$start_date,$end_date];
    $this->insert_into("tr_$room_number",$cols_string,$vars);
  }

  function is_occupied($date){

  }


}