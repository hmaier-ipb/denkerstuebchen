<?php
//starting a session to save a the language form $_GET["lang"]
session_start();

//require("../Smarty/libs/Smarty.class.php");
require("D:/inetpub/Smarty/libs/Smarty.class.php");
require("include/classes/input_form.php");
require("include/classes/calender.class.php");

//creating content, calender & smarty
$form = new input_form();
$cal = new calender();
$smarty_object = new Smarty();
$smarty_object->left_delimiter = '<!--{';
$smarty_object->right_delimiter = '}-->';


if (isset($_POST["action"])) {
  switch ($_POST["action"]) {
    case "form-data": //converting received form-data and send it to a receiver "bibliothek@ipb-halle.de"
      $response = $form->validation();
      print(json_encode($response));
      //$form->send_email();
      //print(json_encode("email send successfully"));
      break;

    case "get-lang": //choosing the language for the error messages
      $_SESSION["lang"] == "de" ? $output = "de" : $output = "en";
      print(json_encode($output));
      break;

    case "prev_month": //prev month pressed
      $prev_month = strtotime("-1 Month",$_SESSION["displayed_month"]);
      print(json_encode($cal->create_calender($prev_month,$_SESSION["lang"],$_SESSION["room_number"])));
      $_SESSION["displayed_month"] = $prev_month;// setting a new current time
      break;

    case "current_month"://current month pressed
      $current_month = time();
      print(json_encode($cal->create_calender($current_month,$_SESSION["lang"],$_SESSION["room_number"])));
      $_SESSION["displayed_month"] = $current_month;// setting a new current time
      break;

    case "next_month"://next month pressed
      $next_month = strtotime("+1 Month",$_SESSION["displayed_month"]);
      print(json_encode($cal->create_calender($next_month,$_SESSION["lang"],$_SESSION["room_number"])));
      $_SESSION["displayed_month"] = $next_month;//$setting a new current time
      break;

    case "room_select"://
      $room = $_POST["room"];
      $_SESSION["room_number"] = $room;
      $output = json_encode($cal->create_calender($_SESSION["displayed_month"],$_SESSION["lang"],$_SESSION["room_number"]));
      print($output);
      break;

    case "submit_dates":
      $start_date = $_POST["start_date"];
      $end_date = $_POST["end_date"];
      $message = $form->check_dates($start_date,$end_date,$_SESSION["lang"]);
      //TODO: CHECKING IF DATES ARE OCCUPIED
      print(json_encode($message));
      break;

    default:
      print("invalid action");
  }
}


if (isset($_GET["lang"])) {
  $_SESSION["lang"] = $_GET["lang"];
  $lang_array = $form->language($_SESSION["lang"]);

  //**********************
  //CREATING THE CALENDER
  //**********************

  $current_time = time();

  $_SESSION["displayed_month"] = $current_time;

  $cal_vars = $cal->create_calender($_SESSION["displayed_month"],$_SESSION["lang"],1); //passing the current unix time and the language into the calender function

  $vars = [
    "guest" => $lang_array[0],
    "employee" => $lang_array[1],
    "name" => $lang_array[2],
    "surname" => $lang_array[3],
    "department" => $lang_array[4],
    "departments" => $form->departments(),
    "phone" => $lang_array[5],
    "email" => $lang_array[6],
    "button" => $lang_array[7],
    "title" => $lang_array[8],
    "instruction" => $lang_array[9],
    "output" => $lang_array[10],
    "lang" => $lang_array[11],
    "calender" => $cal_vars

  ];



  $smarty_object->assign($vars);
  $smarty_object->display("index.html");
}