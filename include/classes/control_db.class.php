<?php

require_once("easy_pdo_db.class.php");
require_once("validation.class.php");

class control_db extends easy_pdo_db
{
  function __construct()
  {
    parent::__construct();
  }
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
      $response = $this->make_reservation($user_id,$start_date,$end_date,$room_number);
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
    $occupied_dates = $this->get_occupied_dates("tr_$room_number");
    $validate = new validation();
    $response = $validate->is_period_occupied($start_date,$end_date,$occupied_dates);
    //todo: use function from validate here
    /*foreach($occupied_dates as $value){
      $start_db = $value[0];
      $end_db = $value[1];
      if($start_date < $start_db && $end_date > $end_db){
        $response = ["occupation_in_period"];
      }
    }*/
    if($response[0] !== "occupation_in_period"){
      $cols_string = "user_id, start_date, end_date";
      $vars = [$user_id,$start_date,$end_date];
      $this->insert_into("tr_$room_number",$cols_string,$vars);
      $response = ["success"];
    }

    return $response;
  }

  function get_occupied_dates($table)
  {
    $num_rows = $this->count_rows($table);
    //error_log($num_rows);
    if($num_rows !== 0){
    for($x=1;$x<=$num_rows;$x++){
      $row = $this->get_row_by_id($table,$x);
      //error_log(json_encode($row));
      $occupied_dates[] = [$row["start_date"],$row["end_date"]];
    }
    }else{
      $occupied_dates = [[0,0]];
    }
    return $occupied_dates;
  }


}