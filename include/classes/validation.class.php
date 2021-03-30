<?php

require_once("easy_pdo_db.class.php");
require_once("control_ldap.class.php");

class validation
{


  function check_form_data(){
    $response = [];
    $full_name = $_POST["full_name"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    //error_log(json_encode($_POST));

    if(preg_match("/^[A-Za-zöäüÖÄÜß]{2,}, [A-Za-zöäüÖÄÜß]{2,}$/",$full_name) == 0){
      $response[] = 0;
    }
    if($start_date == ""){
      $response[] = 1;
    }
    if($end_date == ""){
      $response[] = 2;
    }
    //error_log(json_encode($response));
    return $response;
  }

  function end_earlier_start($start,$end){
    $start = strtotime($start);
    $end = strtotime($end);

    if($start > $end && intval($end) !== 0){ // checking if start date is higher than end date
      $response = ["end_earlier_start"];
    }else{
      $response = [0];
    }
    return $response;
  }

  function is_in_between($start_date,$end_date,$date){//date is in between already reserved dates
    $ts_start = strtotime($start_date);
    $ts_end = strtotime($end_date);
    $ts_date = strtotime($date);
    if($ts_start<=$ts_date && $ts_end >= $ts_date){
      return True; //is in between
    }else{
      return False; //is not in between
    }
  }

  function is_date_occupied($date, $occupied_dates){
    //$occupied_dates -> [[start,end],[start,end]]
  // checks the occupation just for one room
    foreach ($occupied_dates as $value){
      $start_date = $value[2];
      $end_date = $value[3];
      $res_status = $value[4];
      if($this->is_in_between($start_date,$end_date,$date)==true){
        return $res_status;
      }
    }
    return false;
  }

  function is_period_available($start_date_string,$end_date_string,$occupied_dates){
    $start_date = strtotime($start_date_string);
    $end_date = strtotime($end_date_string);
    foreach($occupied_dates as $value){
      if($value[0] !== 0 && $value[1] !== 0){
        $start_db = strtotime($value[0]);
        $end_db = strtotime($value[1]);
      }else{
        return true;
      }

      if($start_date <= $start_db && $end_date >= $end_db){
        //selected period encloses reservation

//        error_log("Case 1");
//        error_log(json_encode($start_date_string. ", " .$end_date_string));
//        error_log(json_encode($value));
        return false;
      }
      if($start_date >= $start_db && $end_date <= $end_db){ //
        //selected period intersects with reservation

//        error_log("Case 2");
//        error_log(json_encode($start_date_string. ", " .$end_date_string));
//        error_log(json_encode($value));
        return false;
      }
      if($start_date <= $start_db && $end_date >= $start_db){
        //intersects at start_db

//        error_log("Case 3");
//        error_log(json_encode($start_date_string. ", " .$end_date_string));
//        error_log(json_encode($value));
        return false;
      }
      if($start_date <= $end_db  && $end_date >= $end_db){
        //intersects at end_db

//        error_log("Case 4");
//        error_log(json_encode($start_date_string. ", " .$end_date_string));
//        error_log(json_encode($value));
        return false;
      }
    }
    return true;
  }


  function is_greater_than_four_months($start_date,$end_date){
    $ts_start = strtotime($start_date);
    $ts_end = strtotime($end_date);
    $ts_start_plus_four_months = strtotime("+4 Months",$ts_start);
    if($ts_end > $ts_start_plus_four_months){
      return ["period_greater_than_four_months"];
    }
    return [0];
  }

  function start_in_the_past($start_date){
    $ts_start = strtotime($start_date);
    $ts_today = strtotime("00:00:00",$_SESSION["today_time"]);
    if($ts_start < $ts_today){
      return ["start_date_in_past"];
    }
    return [0];
  }

  function date_validation($start_date,$end_date){
    $end_earlier_start = $this->end_earlier_start($start_date,$end_date);//start not bigger than end
    //$occupied = $this->encloses_period($start_date,$end_date,$occupied_dates);
    $four_months = $this->is_greater_than_four_months($start_date,$end_date);

    if($end_earlier_start[0] !== 0){
      return $end_earlier_start;
    }
    elseif ($four_months[0] !== 0){
      return $four_months;
    }

    return ["no_error"];
  }


  function staff_login($user,$pwd){
    $ldap = new control_ldap();
    return $ldap->auth_user($user,$pwd);
  }



}