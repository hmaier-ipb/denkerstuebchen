<?php


require_once ("include/classes/db_denkerstuebchen.class.php");

class calender extends db_denkerstuebchen
{



  function __construct(){
    $_SESSION["end"] = false;
    $_SESSION["color"] = "green";

  }


  function first_weekday_month($weekday_today, $monthday_today): int
  {
    // $weekday_today = date("N") -> numeric representation from 1(Mo) to 7(So)
    // $monthday_today = date("j") -> day of the month 1-31
    for($i = $monthday_today-1;$i>=1;$i--){
      $monthday_today -= 1;
      $weekday_today -= 1;
      if($weekday_today<1){$weekday_today += 7;}
    }
    return $weekday_today;
  }

  function last_weekday_month($weekday_today,$monthday_today,$days_this_month): int
  {
    for($i=$monthday_today;$i<$days_this_month;$i++){
      if($weekday_today == 7){
        $monthday_today +=1;
        $weekday_today = 1;
      }else{
        $monthday_today += 1;
        $weekday_today += 1;
      }
    }
    return $weekday_today;
  }


  function create_calender($current_time,$language,$room_number=1): string
  {
    $_SESSION["room_number"] = $room_number;
    $prev_month = strtotime("-1 Month",$current_time); //unix timestamp for the previous month from today
    $next_month = strtotime("+1 Month",$current_time); //unix timestamp for the next month from today

    $days_prev_month = date("t",$prev_month); // number of days in previous month

    $weekday_today = date("N",$current_time);   // -> numeric representation from 1(Mo) to 7(So)
    $monthday_today = date("j",$current_time);  // -> day of the month 1-31
    $days_this_month = date("t",$current_time); // -> number of days this month
    $month = date("m",$current_time); // -> this month e.g. Jan = 01
    $year = date("Y",$current_time); // -> this year e.g. 2021

    $first_weekday_month = $this->first_weekday_month($weekday_today,$monthday_today);
    $last_weekday_month = $this->last_weekday_month($weekday_today,$monthday_today,$days_this_month);


    $weekday_count = 0;// detect when to create a new row
    $weekdays_array = [
      ["Monday", "Montag"],
      ["Tuesday", "Dienstag"],
      ["Wednesday","Mittwoch"],
      ["Thursday","Donnerstag"],
      ["Friday","Freitag"],
      ["Saturday","Samstag"],
      ["Sunday","Sonntag"]
    ];

    $used_language = [];

    if($language == "de"){
      foreach($weekdays_array as $day){
        $used_language[] = $day[1];
      }
    }else{
      foreach($weekdays_array as $day){
        $used_language[] = $day[0];
      }
    }

    $language == "de" ? $room_name = "Denkerstübchen" : $room_name = "Thinkersroom";

    $monate = ["Januar", "Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];

    $language == "de" ? $current_month = $monate[date("n",$current_time)-1] : $current_month = date("F",$current_time);

    //**********************
    //STRING CREATION BEGINS
    //**********************
    $calender_string = "<p id='room_month_year'>" . $room_name . " " . $room_number . " " . $current_month. " " .date("Y",$current_time) . "</p> "; //current month
    $calender_string .= $this->room_select($_SESSION["lang"]);
    $calender_string .= "<table class='calender_table'>"; // calender string which contains the HTML

    //WEEKDAYS HEADER
    $calender_string .= "<tr>";
    foreach($used_language as $day){
      $calender_string .= "<th class='days'>$day</th>";
    }
    $calender_string .= "</tr>";

    //PREVIOUS MONTH
    //fill in missing weekdays (from Mo) to $first_weekday_month
    //GRAY COLOURING
    if($first_weekday_month !== 1){
      $days_to_fill = $first_weekday_month-1;
      $calender_string .= "<tr>";
      for($i=($days_prev_month-$days_to_fill)+1;$i<=$days_prev_month;$i++){ // the + 1 has to be added because a for loop start at index zero [0], so a additional day would be added

        $weekday_count += 1;
        $calender_string .= "<td class='prev_month' style='background-color: #666666'>$i</td>";//table cells
      }
    }

    //CURRENT DISPLAYED MONTH
    //RED=OCCUPIED/GREEN=FREE COLOURING FOR THE CELLS
    for($i=1;$i<=$days_this_month;$i++){

      //Formatting the string
      $i<10?$iterated_date = "0".$i.".".$month.".".$year."":$iterated_date = "".$i.".".$month.".".$year.""; //string of iterated

      //error_log(json_encode($iterated_date));

      if($weekday_count == 7){
        $weekday_count = 0;
        $calender_string .= "</tr>";//close a row
        $calender_string .= "<tr>"; //open a row
      }

      $this->set_color($iterated_date);
      $color = $_SESSION["color"];
      $weekday_count += 1;

      $calender_string .= "<td class='current_month' id='$iterated_date' style='background-color: $color'>$i</td>";//table cells


    }


    //creating cells for next month
    //fill in missing weekdays to the end of this week
    if($last_weekday_month !== 7){
      $days_next_month = 1; // helper-var to set days of next month
      for($i = $last_weekday_month;$i<7;$i++){
        $calender_string .= "<td class='next_month' style='background-color: #666666'>$days_next_month</td>";//table cells
        $days_next_month +=1;
      }
      $calender_string .= "</tr>";//closing the last row
    }

    $calender_string .= "</table>";//closing the table

    return $calender_string."<br>";
  }

  function set_color($date){

    // TODO: LOADING THE DATABASE TO CHECK THE OCCUPATION STATE OF A CERTAIN ROOM


    $db_start_date = "0";// search in database
    $db_end_date = "0";// search in database

    if($_SESSION["end"] == true){
      $_SESSION["color"] = "green";
    }

    if($date == $db_start_date){
      $_SESSION["end"] = false;
      $_SESSION["color"] = "red";
    }
    if($date == $db_end_date){
      $_SESSION["end"] = true; //setting $_SESSION["end"] so the next iteration will be green again
    }

  }

  function room_select($lang){
    $num_rooms = 5;
    $lang == "de" ? $room = "Denkerstübchen" : $room = "Thinkersroom";
    $lang == "de" ? $choose = "Auswählen" : $choose = "choose";
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

  function month_buttons($lang){
    $lang == "de" ? $prev = "Vorheriger Monat" : $prev = "Previous Month";
    $lang == "de" ? $current_month = "Aktueller Monat" : $current_month = "Current Month";
    $lang == "de" ? $next = "Nächster Monat" : $next = "Next Month";

    $output = "<button id='prev_month' class='btn calender_btn'>$prev</button><br>";
    $output .= "<button id='current_month' class='btn calender_btn'>$current_month</button><br>";
    $output .= "<button id='next_month' class='btn calender_btn'>$next</button>";

    return $output;
  }


}