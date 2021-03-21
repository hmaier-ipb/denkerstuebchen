<?php

require_once("validation.class.php");
require_once("control_db.class.php");


class language_distribution
{

  /**
   * @var string[][]
   */
  public array $lang_array;
  /**
   * @var control_db
   */
  public control_db $control_db;
  /**
   * @var validation
   */
  public validation $validate;

  function __construct(){

    $this->control_db = new control_db();
    $this->validate = new validation();
    $this->lang_array = [
      ["Gast","Guest"],
      ["Mitarbeiter", "Employee"],
      ["Nachname, Vorname","Lastname, Firstname"],
      ["Nachname","Surname"],
      ["hausinterne Telefonnummer","in-house Telephone Number"],
      ["Email","Email"],
      ["Abteilung","Department"],
      ["Reservierung einreichen","Make Reservation"],
      ["Denkerstübchen Reservierung", "Thinkers-Room Reservation"],
      ["Geben Sie ihre Daten, ein um ein Denkerstübchen zu reservieren","Enter your credentials to reserve a thinkers-room"],
      ["Ungültige Eingabe", "Invalid Input!"],
      ["de","eng"],
      ["Geben Sie Ihren Usernamen ein","Type in your username"],
      ["Zeitraum","Time-Period"],
      ["Beginn: ","Start: "],
      ["Ende: ","End: "],
      ["Geben Sie einen Reservierungszeitraum ein.","Type in a period in which you want to reserve a thinker's-room."],
      ["Beginn - Ende [dd.mm.yyyy]","Start - End [dd.mm.yyyy]"],
      ["Nach nächsten verfügbaren Zeitraum suchen. <br>(4 Monate = 17 Wochen)Wochenanzahl","Search for next available time period. <br> (4 Months = 17 Weeks)Number of Weeks"],
      ["Prüfen","Check"],
    ];
  }

  function language($l){
    //array for english and german terms
    $used_lang = [];
    $language_length = count($this->lang_array)-1;
    switch($l){
      case "de":
        for($i=0;$i<=$language_length;$i++){$used_lang[] = $this->lang_array[$i][0];}
        break;
      default:
        //returns english if no matching case is found
        for($i=0;$i<=$language_length;$i++){$used_lang[] = $this->lang_array[$i][1];}

    }
    //returns a language array which can be assigned for smarty
    return $used_lang;
  }

//  function departments(){
//    $departments_array = [
//      "AdmIN",
//      "BPI",
//      "MSV",
//      "NWC",
//      "SZB",
//      "UNG Voiniciuc",
//      "UNG Weissenborn",
//      "FG BASDA",
//      "FG Proteome Analytics"
//    ];
//
//    $output = "";
//    foreach($departments_array as $index){
//      $output .= "<option value='$index'>$index</option>";
//    }
//    return $output;
//  }

  function send_email(){
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $start_date = $_POST["start_date"];
    $room_number = $_POST["room_number"];
    $end_date = $_POST["end_date"];
    //$receiver = "bibliothek@ipb-halle.de,$email";
    $receiver = "hmaier@ipb-halle.de,$email";
    $subject = "THINKERS-ROOM RESERVATION";

    $m = "<html>";
    $m .= "<head>";
    $m .= "<style> tr,td{border: solid 2px #666666;border-collapse: collapse}</style>";
    $m .= "</head>";
    $m .= "<body>";

    $m .= "";

    $m .= "<table  style='border: solid 2px #666;' >";
    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Name</b></td>";
    $m .= "<td>$full_name</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Room Number</b></td>";
    $m .= "<td>$room_number</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Start Date</b></td>";
    $m .= "<td>$start_date</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>End Date</b></td>";
    $m .= "<td>$end_date</td>";
    $m .= "</tr>";

    $m .= "";
    $m .= "";
    $m .= "</table>";
    $m .= "</body>";
    $m .= "</html>";


    $headers = "From: " . "noreply@ipb-halle.de" . "\r\n";
    $headers .= "Reply-To: ". "hmaier@ipb-halle.de" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";



    ini_set("SMTP","mail.ipb-halle.de");
    ini_set("smtp_port",25);
    return mail($receiver,$subject,$m,$headers);

  }

  function check_for_errors(){//called when send button clicked, returns ["response_message",TRUE/FALSE]
    $message = "";
    $error = [];

    $error_codes = $this->validate->check_form_data();

    if(count($error_codes)==0){
      return $this->database_response();
    }else{//generating error messages
     switch ($_SESSION["lang"]){
       case "de":
         if(in_array(0,$error_codes)){$error[] = "Benutzername";}//username error
         if(in_array(1,$error_codes)){$error[] = "Startdatum";}//start_date error
         if(in_array(2,$error_codes)){$error[] = "Enddatum";}//end_date error
         $l_error = count($error);
         $message .= "Bitte geben Sie ein/en ";
         switch($l_error){
           case 1:
             $message .=  $error[0] ." an.";
             break;
           case 2:
             $message .= $error[0] . "und ". $error[1] ." an.";
             break;
           case 3:
             $message .= $error[0] .", ". $error[1]. " und ".$error[2] . " an.";
         }
         break;
       case "eng":
         if(in_array(0,$error_codes)){$error[] = "Username";}//username error
         if(in_array(1,$error_codes)){$error[] = "Start Date";}//start_date error
         if(in_array(2,$error_codes)){$error[] = "End Date";}//end_date error
         $l_error = count($error);
         $message .= "Please provide ";
         switch($l_error){
           case 1:
            $message .=  $error[0] .".";
            break;
           case 2:
             $message .= $error[0] . "and ". $error[1];
             break;
           case 3:
             $message .= $error[0] .", ". $error[1]. " and ".$error[2] . ".";
         }
         break;
     }

      $response[] = $message;
      $response[] = False;
      return $response;
    }

  }


/*  function compare_dates($start_date,$end_date){
    $vali = $this->validate->start_smaller_end($start_date,$end_date);
    if($vali == False){
    //$_SESSION["lang"] == "de" ? $output = "Enddatum kann nicht kleiner als Startdatum sein.": $output = "End Date cannot be smaller than Start Date.";
      $output = "error";
    }else{
      $output = null;
    }
    return $output;
  }*/

//  function room_select(){
//    $num_rooms = 5;
//    $_SESSION["lang"] == "de" ? $room = "Denkerstübchen" : $room = "Thinkersroom";
//    //$_SESSION["lang"] == "de" ? $choose = "Auswählen" : $choose = "Choose";
//    $output = "<div id='room_selection_div'>";
//    $output .= "<select name='rooms' id='room_selection' class='room_selection'>";
//    for($i = 1;$i<=$num_rooms;$i++){
//      $output .= "<option value='$i'>$room $i</option>";
//    }
//    $output .= "</select>";
//    //$output .= "<button id='btn_select_room' class='btn'>$choose</button>";
//    $output .= "</div>";
//    return $output;
//  }

  function database_response(){
    //error_log("Database called!");
    $db_call = $this->control_db->new_reservation();
    //error_log(json_encode("db call"));
    //error_log(json_encode($db_call));
    if($db_call[0] == "success"){//calling database validation
      $_SESSION["lang"] == "de" ? $response[] = "Anfrage erfolgreich!" : $response[] = "Sucessful query!" ;
      $response[] = True;
      //$this->send_email();
    }else{
      //$response[] = $this->control_db->new_reservation();
      $response[] = $_SESSION["lang"] == "de" ? $response[] = "Anfrage nicht erfolgreich! :-(": $response[] = "Unsucessful query! :-(";
      $response[] = False;
    }
    return $response;
  }


//  function process_db_errors($db_response){
//    //error_log(json_encode($db_response));
//    switch($db_response[0]){
//      case "already_exists_in_user":
//        return $_SESSION["lang"] == "de" ? "Benutzer schon bekannt." : "User already know.";
//      case "existing_reservation":
//        $start_date = $db_response[1]["start_date"];
//        $end_date = $db_response[1]["end_date"];
//        return $_SESSION["lang"] == "de" ? "Eine Reservierung vom Denkerstübchen $db_response[2] ab dem $start_date bis zum $end_date ist schon vorhanden. " : "A reservation for thinkers-room $db_response[2] from the $start_date to the $end_date is already existing .";
//      case "occupation_in_period":
//        return $_SESSION["lang"] == "de" ? "Belegung im ausgewählten Zeitraum, bitte wählen Sie einen anderen Raum/Zeitraum für Ihre Reservierung." : "Occupation in selected period, please choose a different room/period for your reservation";
//      default:
//        return $_SESSION["lang"] == "de" ? "Etwas seltsames ist geschehen..." : "Something weird happened...";
//    }
//
//  }

//  function process_date_errors($start_date,$end_date){
//    $lang = $_SESSION["lang"];
//    $validate = new validation();
//    $date_validation_response = $validate->date_validation($start_date,$end_date);
//
//    error_log(json_encode($date_validation_response));
//
//    switch($date_validation_response[0]) {
//      case "start_bigger_end":
//        $message =  $lang == "de" ? "Enddatum kann nicht kleiner als Startdatum sein.": "End date cannot be smaller than start date.";
//        break;
//      case "occupation_in_period":
//        $message =  $lang == "de" ? "Im ausgewählten Zeitraum befindet sich schon eine Reservierung. Bitte wählen Sie einen anderen Zeitraum.": "There is a occupation in the corresponding period. Please select a different time period.";
//        break;
//      case "period_greater_than_four_months":
//        $message =  $lang == "de" ? "Laut Direktoriumsbeschluss vom 23.09.2019 ist die Belegung des Denkerstübchen auf einen Zeitraum von <b>vier Monaten</b> beschränkt.": "A thinker's room can be booked for a maximum of <b>4 months</b>.";
//        break;
//      case "start_date_in_past":
//        $message =   $lang == "de" ? "Beginn der Reservierung kann nicht in der Vergangenheit liegen.": "Start Date cannot be in the past.";
//        break;
//      default:
//        $message = 0;
//    }
//    return $message;
//    if($date_validation_response[0] == 0){
//      return 0;
//    }elseif ($date_validation_response[0] == "start_bigger_end"){
//      return $lang == "de" ? "Enddatum kann nicht kleiner als Startdatum sein.": "End date cannot be smaller than start date.";
//    }elseif ($date_validation_response[0] == "occupation_in_period"){
//      return $lang == "de" ? "Im ausgewählten Zeitraum befindet sich schon eine Reservierung. Bitte wählen Sie einen anderen Zeitraum.": "There is a occupation in the corresponding period. Please select a different time period.";
//    }elseif ($date_validation_response[0] == "period_greater_than_four_months"){
//      return $lang == "de" ? "Laut Direktoriumsbeschluss vom 23.09.2019 ist die Belegung des Denkerstübchen auf einen Zeitraum von <b>vier Monaten</b> beschränkt.": "A thinker's room can be booked for a maximum of <b>4 months</b>.";
//    }elseif ($date_validation_response[0] == "start_date_in_past"){
//      return $lang == "de" ? "Beginn der Reservierung kann nicht in der Vergangenheit liegen.": "Start Date cannot be in the past.";
//    }



}
