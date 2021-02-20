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
spl_autoload_unregister('__autoloadMyClasses');
#include("include/classes/language_distribution.class.php");
#include("include/classes/calender.class.php");

$ldap->connect();

//PAGE GETS LOADED FOR THE FIRST TIME
if (isset($_GET["lang"])) {
  $smarty_object = new Smarty();
  $smarty_object->left_delimiter = '<!--{';
  $smarty_object->right_delimiter = '}-->';

  $_SESSION["room_number"] = 1;
  $_SESSION["lang"] = $_GET["lang"];

  $lang_array = $lang_dist->language($_SESSION["lang"]);

  //**********************
  //CREATING THE CALENDER
  //**********************

  $current_time = time();
  $_SESSION["global_current_time"] = $current_time;
  $display_calender = $cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]); //passing the current unix time and the language into the calender function

  $vars = [
    "guest" => $lang_array[0],
    "employee" => $lang_array[1],
    "name" => $lang_array[2],
    "surname" => $lang_array[3],
    "department" => $lang_array[6],
    "departments" => $lang_dist->departments(),
    "phone" => $lang_array[4],
    "email" => $lang_array[5],
    "button" => $lang_array[7],
    "title" => $lang_array[8],
    //"instruction" => $lang_array[9],
    "output" => $lang_array[10],
    "lang" => $lang_array[11],
    "calender" => $display_calender,
    "room_selection" => $lang_dist->room_select(),
    "room_month_year" => $cal->room_month_year($current_time),
    "month_buttons" => $cal->month_buttons()

  ];
  $smarty_object->assign($vars);
  $smarty_object->display("index.html");
}

//AJAX REQUESTS
if (isset($_POST["action"])) {

  switch ($_POST["action"]) {
    case "form-data": //converting received form-data and send it to a receiver "bibliothek@ipb-halle.de"
      //error_log(json_encode($_POST));
      $response = $lang_dist->input_response();
      $response[] = $cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]);
      error_log(json_encode($response));
      print(json_encode($response));
      break;

    case "get-lang": //choosing the language for the error messages
      $_SESSION["lang"] == "de" ? $output = "de" : $output = "en";
      print(json_encode($output));
      break;

    case "prev_month": //prev month pressed
      $prev_month = strtotime("-1 Month",$_SESSION["global_current_time"]);
      $response[] = $cal->room_month_year($prev_month);
      $response[] = $cal->create_calender($prev_month,$_SESSION["lang"]);
      print(json_encode($response));
      $_SESSION["global_current_time"] = $prev_month;// setting a new current time
      break;

/*    case "current_month"://current month pressed
      $current_month = time();
      print(json_encode($cal->create_calender($current_month,$_SESSION["lang"])));
      $_SESSION["global_current_time"] = $current_month;// setting a new current time
      break;*/

    case "next_month"://next month pressed
      $next_month = strtotime("+1 Month",$_SESSION["global_current_time"]);
      $response[] = $cal->room_month_year($next_month);
      $response[] = $cal->create_calender($next_month,$_SESSION["lang"]);
      print(json_encode($response));
      $_SESSION["global_current_time"] = $next_month;//$setting a new current time
      break;

    case "room_select"://
      $room = $_POST["room"];
      $_SESSION["room_number"] = $room;
      $response[] = $cal->room_month_year($_SESSION["global_current_time"]);
      $response[] = $cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]);
      //$output = json_encode($cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]));
      print(json_encode($response));
      break;

    case "submit_dates":
      $validate = new validation();
      $start_date = $_POST["start_date"];
      $end_date = $_POST["end_date"];
      $message[] = $cal->create_calender($_SESSION["global_current_time"],$_SESSION["lang"]);
      $message[] = $validate->compare_dates($start_date,$end_date);
      print(json_encode($message));
      break;
    case "search_user":
      $ldap = new control_ldap();
      $response = $ldap->search_user($_POST["search_user"]);
      break;

    default:
      print("invalid action");
  }
}

