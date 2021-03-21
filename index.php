<?php
//starting a session to save a the language form $_GET["lang"]
define("INCLUDE_DIR", "include/classes/");

session_start();

include("../Smarty/libs/Smarty.class.php");

function __autoloadMyClasses($className) {
  require_once(INCLUDE_DIR . $className . ".class.php");
}
spl_autoload_register('__autoloadMyClasses');
$lang_dist = new language_distribution();
$cal = new calender();
$ldap = new control_ldap();
$validate = new validation();
$control_db = new control_db();
$cal_02 = new calender_02();
spl_autoload_unregister('__autoloadMyClasses');
#include("include/classes/language_distribution.class.php");
#include("include/classes/calender.class.php");



//PAGE GETS LOADED FOR THE FIRST TIME
if (isset($_GET["lang"])) {
  $smarty_object = new Smarty();
  $smarty_object->left_delimiter = '<!--{';
  $smarty_object->right_delimiter = '}-->';

  $_SESSION["room_number"] = 1;
  $_SESSION["lang"] = $_GET["lang"];

  $lang_array = $lang_dist->language($_SESSION["lang"]);

  //GET ALL USERS FROM LDAP SERVER
//  $all_users = $ldap->get_ad_data("");//empty string searches for all users
//  $_SESSION["all_users"] = $all_users;

  //GET ALL USERS FROM AT HOME
  $all_users = [];//$ldap->get_all_users();

//  $count = 0;
//  foreach($all_users as $user){
//    print(json_encode($user));
//    print("<br>");
//    $count += 1;
//  }
//  error_log(json_encode($count));
//  error_log(json_encode($all_users));

  // CLEAN UP THE DATABASE
  $control_db->delete_old_reservations();

  //**********************
  //CREATING THE CALENDER
  //**********************

  $current_time = time();
  $_SESSION["today_time"] = $current_time;//for validation: is date selected date in the past?
  $_SESSION["global_current_time"] = $current_time;
  //$display_calender = $cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]); //passing the current unix time and the language into the calender function
  $display_calender = $cal_02->create_calender($_SESSION["global_current_time"]); //passing the current unix time and the language into the calender function

  $vars = [
    "guest" => $lang_array[0],
    "employee" => $lang_array[1],
    "name" => $lang_array[2],
    //"surname" => $lang_array[3],
    "department" => $lang_array[6],
    //"departments" => $lang_dist->departments(),
    "phone" => $lang_array[4],
    "email" => $lang_array[5],
    "button" => $lang_array[7],
    "title" => $lang_array[8],
    //"instruction" => $lang_array[9],
    //"output" => $lang_array[10],
    "lang" => $lang_array[11],
    "calender" => $display_calender,
    //"room_selection" => $lang_dist->room_select(), //<!--{$room_selection}-->
    "room_month_year" => $cal_02->room_month_year($current_time),
    "month_buttons" => $cal_02->month_buttons(),
    "user_search_prompt" => $lang_array[12],
    "period" => $lang_array[13],
    "start_date" => $lang_array[14],
    "end_date" => $lang_array[15],
    "suggestion_html" => $ldap->suggestion_html($all_users),
    "date_search_term" => $lang_array[16],
    //"date_search_placeholder" => $lang_array[17]
    "time_period_search_text" => $lang_array[18],
    "search_period_button_text" => $lang_array[19]

  ];
  
  $smarty_object->assign($vars);
  $smarty_object->display("index.html");
}

//AJAX REQUESTS
if (isset($_POST["action"])) switch ($_POST["action"]) {
  case "form-data": //converting received form-data and send it to a receiver "bibliothek@ipb-halle.de"
    //error_log(json_encode($_POST));
    $response[] = $lang_dist->check_for_errors();
    $response[] = $cal_02->create_calender($_SESSION["global_current_time"]);
    //error_log(json_encode($response));
    print(json_encode($response));
    break;

//    case "get-lang": //choosing the language for the error messages
//      //$_SESSION["lang"] == "de" ? $output = "de" : $output = "en";
//      //print(json_encode($output));
//      break;

  case "prev_month": //prev month pressed

    $prev_month = strtotime("-1 Month", $_SESSION["global_current_time"]);
    if(time()<$prev_month) {
      $response[] = $cal_02->room_month_year($prev_month);
      $response[] = $cal_02->create_calender($prev_month);
      $_SESSION["global_current_time"] = $prev_month;

    }else{
      $response[] = $cal_02->room_month_year(time());
      $response[] = $cal_02->create_calender(time());
      $_SESSION["global_current_time"] = time();
    }
    print(json_encode($response));
    // setting a new current time

    break;

  /*    case "current_month"://current month pressed
        $current_month = time();
        print(json_encode($cal->create_calender($current_month,$_SESSION["lang"])));
        $_SESSION["global_current_time"] = $current_month;// setting a new current time
        break;*/

  case "next_month"://next month pressed
    $next_month = strtotime("+1 Month", $_SESSION["global_current_time"]);
    $response[] = $cal_02->room_month_year($next_month);
    $response[] = $cal_02->create_calender($next_month);
    print(json_encode($response));
    $_SESSION["global_current_time"] = $next_month;//$setting a new current time
    break;

//    case "room_select"://
//      $room = $_POST["room"];
//      $_SESSION["room_number"] = $room;
//      $response[] = $cal_02->room_month_year($_SESSION["global_current_time"]);
//      $response[] = $cal_02->create_calender($_SESSION["global_current_time"]);
//      //$output = json_encode($cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]));
//      print(json_encode($response));
//      break;

//    case "submit_dates":
//      $start_date = $_POST["start_date"];
//      $end_date = $_POST["end_date"];
//
//      //$message[] = $cal->create_calender($_SESSION["global_current_time"], $_SESSION["lang"]);
////      $date_response = $validate->date_validation($start_date, $end_date);
////      error_log(json_encode($date_response));
//      $message = $lang_dist->process_date_errors($start_date, $end_date);
//      //error_log(json_encode($message));
//      print(json_encode($message));
//      break;
  case "user_search":
    $all_users = $_SESSION["all_users"];
    $uid = $_POST["uid"];
    $selected_user = [];
    if($uid !=="") {
      for ($i = 0; $i < count($all_users); $i++) {
        if (preg_match("/^$uid/", $all_users[$i][1]) == 1) {
          $selected_user = $all_users[$i];
          $email = $selected_user[3];
          }
        }
    }
    if(isset($email)){
      $res_check = $control_db->has_email_reservation($email);
      error_log("Res_check");
      error_log(json_encode($res_check));
      if($res_check !== false){
       $selected_user[] =  $res_check[0];
       $start_date = $res_check[1]["start_date"];
       $end_date = $res_check[1]["end_date"];
       $selected_user[] = $_SESSION["lang"] == "de" ? "Eine Reservierung vom Denkerstübchen $res_check[2] ab dem $start_date bis zum $end_date ist schon vorhanden. " : "A reservation for thinkers-room $res_check[2] from the $start_date to the $end_date is already existing .";
      }
    }
    //error_log(json_encode($selected_user));
    print(json_encode($selected_user));
    break;
  case "date_search":
    //validating the dates
    //testing if there is a free room
    switch (true){
      case isset($_POST["start_date"]):
        $ts_start = strtotime($_POST["start_date"]);
        $today = strtotime("00:00:00",time());
//        error_log(json_encode($ts_start));
//        error_log(json_encode($today));
        if($ts_start >= $today){
          $_SESSION["start_date"] = $_POST["start_date"];
          $response = ["enable"];
        }else{
          $response = ["do_not_enable"];
        }
        //for debugging database function
        //$response = ["enable"];
        print(json_encode($response));
        break;
      case isset($_POST["end_date"]):
        $start_date = $_SESSION["start_date"];
        $end_date = $_POST["end_date"];
        $validation = $validate->date_validation($start_date,$end_date);
        //error_log(json_encode($validation));
        if( $validation === ["no_error"]){
          $there_is_a_free_room = false;
          $rooms_array = $control_db->search_free_room($start_date,$end_date);
          //$rooms_array = [false,false,false,false,false];
          foreach($rooms_array as $room){
            if($room !== false){ //choosing the first room in list that is free
              $there_is_a_free_room = True;
              $response[] = "free_room";
              $response[] = $room;
              print(json_encode($response));
              break;
            }
          }
          if($there_is_a_free_room == false){
            $response[] = "no_free_room";
            $response[] = $_SESSION["lang"] == "de" ? "Im ausgewählten Zeitraum befindet sich kein freier Raum." : "There is no free room in the selected period.";
            print(json_encode($response));
          }
        }else{ // an error occurred
          $message[] = $validation[0];
          switch($validation[0]){
            case "end_earlier_start":
              $message[] = $_SESSION["lang"] == "de" ? "Enddatum kann nicht kleiner als das Startdatum sein." : "End Date cannot be smaller than start date.";
              break;
            case  "period_greater_than_four_months":
              $message[] =  $_SESSION["lang"] == "de" ? "Laut Direktoriumsbeschluss vom 23.09.2019 ist die Belegung des Denkerstübchen auf einen Zeitraum von <b>vier Monaten</b> beschränkt.": "A thinker's room can be booked for a maximum of <b>4 months</b>.";
              break;
          }
          print(json_encode($message));
        }
    }
    break;
  case "period_search";
    $week_num = $_POST["week_num"];
    $response[] = $control_db->next_free_period($week_num);
    print(json_encode($response));
    break;
  default:
    print("invalid action");


}

