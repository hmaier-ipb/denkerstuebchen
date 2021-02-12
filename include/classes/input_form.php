<?php

require_once ("include/classes/db_denkerstuebchen.class.php");

class input_form extends db_denkerstuebchen
{


  /**
   * @var string[][]
   */
  public array $lang_array;

  function __construct(){
    $this->lang_array = [
      ["Gast","Guest"],
      ["Mitarbeiter", "Employee"],
      ["Vorname","Name"],
      ["Nachname","Surname"],
      ["Abteilung","Department"],
      ["hausinterne Telefonnummer","in-house Telephone Number"],
      ["hausinterne Email","in-house Email"],
      ["Abschicken","Send"],
      ["Denkerstübchen Reservierung", "Thinkers-Room Reservation"],
      ["Geben Sie ihre Daten, ein um ein Denkerstübchen zu reservieren","Enter your credentials to reserve a thinkers-room"],
      ["Ungültige Eingabe", "Invalid Input!"],
      ["de","eng"]
    ];
  }

  function language($l){
    //array for english and german terms

    $output = [];
    $language_length = count($this->lang_array)-1;
    switch($l){
      case "de":
        for($i=0;$i<=$language_length;$i++){$output[] = $this->lang_array[$i][0];}
        break;
      default:
        //returns english if no matching case is found
        for($i=0;$i<=$language_length;$i++){$output[] = $this->lang_array[$i][1];}

    }
    //returns a language array which can be assigned for smarty
    return $output;
  }

  function departments(){
    $departments_array = [
      "AdmIN",
      "BPI",
      "MSV",
      "NWC",
      "SZB",
      "UNG Voiniciuc",
      "UNG Weissenborn",
      "FG BASDA",
      "FG Proteome Analytics"
    ];

    $output = "";
    foreach($departments_array as $index){
      $output .= "<option value='$index'>$index</option>";
    }
    return $output;
  }

  function send_email(){
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $department = $_POST["department"];
    //start date & end date
    //room number
    $_POST["status"] == "guest"? $status = "Gast": $status = "Mitarbeiter";
    //$receiver = "bibliothek@ipb-halle.de,$email";
    $receiver = "hmaier@ipb-halle.de,$email";
    $subject = "Denkerstuebchen Reservierung";

    $m = "<html>";
    $m .= "<head>";
    $m .= "<style> tr,td{border: solid 2px #666;border-collapse: collapse}</style>";
    $m .= "</head>";
    $m .= "<body>";
    $m .= "<table  style='border: solid 2px #666;' >";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Name</b></td>";
    $m .= "<td>$name</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Nachname</b></td>";
    $m .= "<td>$surname</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Telefon</b></td>";
    $m .= "<td>$phone</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Email</b></td>";
    $m .= "<td>$email</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Abteilung</b></td>";
    $m .= "<td>$department</td>";
    $m .= "</tr>";

    $m .= "<tr style='padding: 10px;'>";
    $m .= "<td><b>Status</b></td>";
    $m .= "<td>$status</td>";
    $m .= "</tr>";
    $m .= "";
    $m .= "";
    $m .= "</table>";
    $m .= "</body>";
    $m .= "</html>";


//    $message = "
//      Name: " . $surname . "\r\n
//      Vorame: " . $name . "\r\n
//      hausinterne Telefonnummer: " . $phone . "\r\n
//      hausinterne Email: " . $email . " \r\n
//      Abteilung: " . $department . "\r\n
//      Status: " . $status . "\r\n
//      ";

    $headers = "From: " . "noreply@ipb-halle.de" . "\r\n";
    $headers .= "Reply-To: ". "hmaier@ipb-halle.de" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";



    ini_set("SMTP","mail.ipb-halle.de");
    ini_set("smtp_port",25);
    return mail($receiver,$subject,$m,$headers);

  }

  function check_dates($start,$end,$language){
    $room = $_SESSION["room_number"];
    $start = strtotime($start);
    $end = strtotime($end);
    // error_log(json_encode("Start Date ".$start));
    // error_log(json_encode("End Date ".intval($end)));
    //TODO: ASKING THE DATABASE IF DATE IS OCCUPIED
    //TODO: ASKING THE DATABASE IF TIME-PERIOD BETWEEN TWO DATES IS OCCUPIED

    if($start > $end && intval($end) !== 0){ // checking if start date is higher than end date
      $language == "de" ? $output = "Enddatum kann nicht kleiner als Startdatum sein.": $output = "End Date cannot be smaller than Start Date.";
    }else{
      $output = null;
    }
    return $output;
  }

  function validation(){
    $message = "";
    $response = [];
    $error = [];

    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    error_log(json_encode($start_date));
    error_log(json_encode($end_date));

    switch ($_SESSION["lang"]){
      case "de":
        for($i=2;$i<6;$i++){$used_lang[]= $this->lang_array[$i][0];}
        $used_lang[] = "Start Datum";
        $used_lang[] = "End Datum";
        break;
      default:
        for($i=2;$i<6;$i++){$used_lang[]= $this->lang_array[$i][1];}
        $used_lang[] = "Start Date";
        $used_lang[] = "End Date";
        break;
    }

    if(preg_match("/^[A-Za-z]{2,}$/",$name) == false){
      $error[] = $used_lang[0];
    }
    if(preg_match("/^[A-Za-z]{2,}$/",$surname) == false){
      $error[] = $used_lang[1];
    }
    if(preg_match("/^\d{4,}$/",$phone) == false){
      $error[] = $used_lang[2];
    }
    if(preg_match("/(\w+|\w+.\w+){3,}@(ipb-halle.de)/",$email) == false){
      $error[] = $used_lang[3];
    }
    if($start_date == "undefined"){
      $error[] = $used_lang[4];
    }
    if($end_date == "undefined"){
      $error[] = $used_lang[5];
    }

    $error_length = count($error);

    if($error_length == 0){
      switch ($_SESSION["lang"]){
        case "de":
          $message .= "Anfrage war erfolgreich!";
          break;
        default:
          $message .= "Query has been successfully!";
          break;
      }
      $response[] = $message;
      $response[] = True;
      $this->db_query();

    }else{
    //generating error message
    switch ($_SESSION["lang"]){//choosing the language
      case "de"://german

        switch ($error_length){//building response depending number of error
          case 1:
            $message .= "Der Punkt <b>". $error[0]."</b> wurde nicht/falsch angegeben.";
            break;
          case 2:
            $message .= "Die Punkte <b>". $error[0]. "</b> und <b>". $error[1] . "</b> wurden nicht/falsch angegeben.";
            break;
          default:// 3 or more errors
            $message .= "Die Punkte ";
            for($i=0;$i<$error_length;$i++){
              if($i==$error_length-1){
                $message .= "und <b>".$error[$i]."</b>";
              }else{
                $message .= "<b>". $error[$i] . "</b>, ";
              }
            }
            $message .= " wurden nicht/falsch angegeben.";
            break;
        }
      break;//closing german case
        default:// opening english case
          switch ($error_length){//building response depending number of error
            case 1:
              $message .= "The points <b>". $error[0]."</b> has not/falsely been specified.";
              break;
            case 2:
              $message .= "The points <b>". $error[0]. "</b> and <b>". $error[1] . "</b> has not/falsely been specified.";
              break;
            default:// 3 or more errors
              $message .= "The points ";
              for($i=0;$i<$error_length;$i++){
                if($i==$error_length-1){
                  $message .= "and <b>".$error[$i]."</b>";
                }else{
                  $message .= "<b>". $error[$i] . "</b>, ";
                }
              }
              $message .= " has not/falsely been specified.";
              break;
          }
        break;//closing english case
      }
      $response[] = $message;
      $response[] = False;
    }

    return $response;
  }

  //inserting values into the database

  function db_query(){
    // TODO: INSERTING THE GIVEN VALUES INTO THE DATABASE

  }






}
