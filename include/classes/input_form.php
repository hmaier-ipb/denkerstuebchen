<?php


class input_form
{



  function language($l){
    //array for english and german terms
    $language = [
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

    $output = [];
    $language_length = count($language)-1;
    switch($l){
      case "de":

        for($i=0;$i<=$language_length;$i++){$output[] = $language[$i][0];}

        break;
      case "eng";

        for($i=0;$i<=$language_length;$i++){$output[] = $language[$i][1];}

        break;
      default:
        //returns english if no matching case is found
        for($i=0;$i<=$language_length;$i++){$output[] = $language[$i][1];}

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





    $message = "      
      Name: " . $surname . "\r\n    
      Vorame: " . $name . "\r\n
      hausinterne Telefonnummer: " . $phone . "\r\n
      hausinterne Email: " . $email . " \r\n
      Abteilung: " . $department . "\r\n
      Status: " . $status . "\r\n 
      ";

    $headers = "From: " . "noreply@ipb-halle.de" . "\r\n";
    $headers .= "Reply-To: ". "hmaier@ipb-halle.de" . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";



    ini_set("SMTP","mail.ipb-halle.de");
    ini_set("smtp_port",25);
    return mail($receiver,$subject,$m,$headers);

  }

  function compare_dates($start,$end,$language){
    $start = strtotime($start);
    $end = strtotime($end);

    /*error_log($start);
    error_log($end);*/

    if($start > $end){
      $language == "de" ? $output = "Enddatum kann nicht kleiner als Startdatum sein.": $output = "End Date cannot be smaller than Start Date.";
    }else{
      $output = null;
    }
    //error_log(json_encode($output));
    return $output;
  }

  function validation(){
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $dep = $_POST["department"];
    $status = $_POST["status"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

  }






}
