<?php


class validation
{
  function regex_form_data(){
    $response = [];
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    if(preg_match("/^[A-Za-z]{2,}$/",$name) == false){
      $response[] = 0;
    }
    if(preg_match("/^[A-Za-z]{2,}$/",$surname) == false){
      $response[] = 1;
    }
    if(preg_match("/^\d{4,}$/",$phone) == false){
      $response[] = 2;
    }
    if(preg_match("/(\w+|\w+.\w+){3,}@(ipb-halle.de)/",$email) == false){
      $response[] = 3;
    }
    if($start_date == "undefined"){
      $response[] = 4;
    }
    if($end_date == "undefined"){
      $response[] = 5;
    }

    return $response;
  }

  function start_smaller_end($start,$end){
    $start = strtotime($start);
    $end = strtotime($end);

    if($start > $end && intval($end) !== 0){ // checking if start date is higher than end date
      $response = False;
    }else{
      $response = True;
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

  function is_occupied($date, $occupied_dates){
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


}