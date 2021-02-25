<?php


class validation
{
  protected control_ldap $ldap;

  function __construct(){
    $this->ldap = new control_ldap();
  }

  function regex_form_data(){
    $response = [];
    $name = $_POST["full_name"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    //error_log(json_encode($_POST));

    if(preg_match("/^[A-Za-z]{2,}, [A-Za-z]{2,}$/",$name) == false){
      $response[] = 0;
    }
    if($start_date == ""){
      $response[] = 1;
    }
    if($end_date == ""){
      $response[] = 2;
    }

    return $response;
  }

  function compare_dates($start,$end){
    $start = strtotime($start);
    $end = strtotime($end);

    if($start > $end && intval($end) !== 0){ // checking if start date is higher than end date
      $response = False;
    }else{
      $response = null;
    }
    return $response;
  }

  function is_in_between($start_date,$end_date,$date){
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
    foreach ($occupied_dates as $value){
      $start_date = $value[0];
      $end_date = $value[1];
      if($this->is_in_between($start_date,$end_date,$date)==true){
        return true;
      }
    }
    return false;
  }

  function is_period_occupied($start_date,$end_date,$occupied_dates){
    $response = [0];
    foreach($occupied_dates as $value){
      $start_db = $value[0];
      $end_db = $value[1];
      if($start_date < $start_db && $end_date > $end_db){
        $response = ["occupation_in_period"];
      }
    }
    error_log(json_encode($response));
    return $response;
  }




}