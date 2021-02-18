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
    $m .= "<style> tr,td{border: solid 2px #666666;border-collapse: collapse}</style>";
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

  function input_response(){//called when send button clicked
    $message = "";
    $response = [];
    $error = [];

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
    $form_data_validation = $this->validate->regex_form_data();//checking inputs with regex

    foreach($form_data_validation as $value){$error[] = $used_lang[$value];}
    $error_length = count($error);


    if($error_length == 0){//regex is ok, data can be send to the database

      return $this->database_response();

    }else{//generating error message because some regex failed

    switch ($_SESSION["lang"]){//choosing the language
      case "de"://german

        switch ($error_length){//building response depending length of error
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
              $message .= "The point <b>". $error[0]."</b> has not/falsely been specified.";
              break;
            case 2:
              $message .= "The points <b>". $error[0]. "</b> and <b>". $error[1] . "</b> have not/falsely been specified.";
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
              $message .= " have not/falsely been specified.";
              break;
          }
        break;//closing english case
      }
      $response[] = $message;
      $response[] = False;
      return $response;
    }
  }

  function compare_dates($start_date,$end_date){
    $func = $this->validate->start_smaller_end($start_date,$end_date);
    if($func == False){
    $_SESSION["lang"] == "de" ? $output = "Enddatum kann nicht kleiner als Startdatum sein.": $output = "End Date cannot be smaller than Start Date.";
    }else{
      $output = null;
    }
    return $output;
  }

  function room_select(){
    $num_rooms = 5;
    $_SESSION["lang"] == "de" ? $room = "Denkerstübchen" : $room = "Thinkersroom";
    $_SESSION["lang"] == "de" ? $choose = "Auswählen" : $choose = "Choose";
    $output = "<div id='room_selection_div'>";
    $output .= "<select name='rooms' id='room_selection' class='room_selection'>";
    for($i = 1;$i<=$num_rooms;$i++){
      $output .= "<option value='$i'>$room $i</option>";
    }
    $output .= "</select>";
    $output .= "<button id='btn_select_room' class='btn'>$choose</button>";
    $output .= "</div>";
    return $output;
  }

  function database_response(){
    //error_log("Database called!");
    $db_call = $this->control_db->new_reservation();
    //error_log($db_call);
    if($db_call == "success"){//calling database validation
      $_SESSION["lang"] == "de" ? $response[] = "Anfrage erfolgreich!" : $response[]= "Sucessful query!" ;
      $response[] = True;
    }else{
      //$_SESSION["lang"] == "de" ? $response[] = "Anfrage nicht erfolgreich! :-(": $response[] = "Unsucessful query! :-(";
      //$response[] = $this->control_db->new_reservation();
      $response[] = $this->process_db_errors($db_call);
      $response[] = False;
    }
    return $response;
  }

  function process_db_errors($db_response){
    switch($db_response[0]){
      case "already_exists_in_user":
        return $_SESSION["lang"] == "de" ? "Benutzer schon bekannt." : "User already know.";
      case "existing_reservation":
        $start_date = $db_response[1]["start_date"];
        $end_date = $db_response[1]["end_date"];
        return $_SESSION["lang"] == "de" ? "Eine Reservierung vom Denkerstübchen $db_response[2] ab dem $start_date bis zum $end_date ist schon vorhanden. " : "A reservation for thinkers-room $db_response[2] from the $start_date to the $end_date is already existing .";
      default:
        return $_SESSION["lang"] == "de" ? "Etwas seltsames ist geschehen..." : "Something weird happened...";
    }

  }




}
