<?php


require_once ("include/classes/db_denkerstuebchen.class.php");

class calender extends db_denkerstuebchen
{
//  public string $color;
//
//  function __construct(){
//    $this->color = $_SESSION["color"];
//  }

  function __construct(){
    $_SESSION["end"] = false;
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


  function create_calender($current_time,$language,$room_number): string
  {


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

    $language == "de" ? $room = "Denkerstübchen" : $room = "Thinkers-Room";
    $monate = ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
    $language == "de" ? $displayed_month = $monate[date("n",$current_time)-1]: $displayed_month = date("F",$current_time);


    //**********************
    //STRING CREATION BEGINS
    //**********************
    $calender_string = "<p>" . $displayed_month . " " .date("Y",$current_time) . "</p>"; //current month
    $calender_string .= "<p>" . $room . " " . $room_number . "</p>";
    $calender_string .= "<table id='calender_table'>"; // calender string which contains the HTML

    //WEEKDAYS HEADER
    $calender_string .= "<tr>";
    foreach($used_language as $day){
      $calender_string .= "<th class='days'>$day</th>";
    }
    $calender_string .= "</tr>";

    //creating cells for previous month
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


    //creating cells and rows for current month
    //RED=OCCUPIED/GREEN=FREE COLOURING FOR THE CELLS
    for($i=1;$i<=$days_this_month;$i++){

      if($i < 10){$i = "0" . $i;}
      $iterated_date = "".$i.".".$month.".".$year.""; //string of iterated_date

      //error_log(json_encode($iterated_date));

      if($weekday_count == 7){
        $weekday_count = 0;
        $calender_string .= "</tr>";//close a row
        $calender_string .= "<tr>"; //open a row
      }

      $this->set_color($iterated_date);
      $color = $_SESSION["color"];

      $weekday_count += 1;
      $calender_string .= "<td class='current_month_days' id='$i.$month.$year' style='background-color: $color'>$i</td>";//table cells

    }


    //creating cells for next month
    //fill in missing weekdays to the end of this week
    if($last_weekday_month !== 7){
      $days_next_month = 1; // helper-var to set days of next month
      for($i = $last_weekday_month;$i<7;$i++){
          if($days_next_month < 10){$days_next_month = "0" . $days_next_month;}
          $calender_string .= "<td class='next_month' style='background-color: #666666'>$days_next_month</td>";//table cells
          $days_next_month +=1;
      }
      $calender_string .= "</tr>";//closing the table
    }

    $calender_string .= "</table>";

    return $calender_string."<br>";
  }

  function set_color($date){

    $start_date = "";// search in database
    $end_date = "";// search in database

    if($_SESSION["end"] == true){
      $_SESSION["color"] = "green";
    }

    if($date == $start_date){
      $_SESSION["end"] = false;
      $_SESSION["color"] = "red";
    }
    if($date == $end_date){
      $_SESSION["end"] = true; //setting $_SESSION["end"] so the next iteration will be green again
    }

  }

  function room_select($num_rooms,$lang){

    $lang == "de" ? $room = "Denkerstübchen" : $room = "Thinkers-Room";
    $lang == "de" ? $choose = "Auswählen" : $choose = "Choose";

    $output = "<select name='rooms' id='rooms' class='rooms'>";
    for($i = 1;$i<=$num_rooms;$i++){
      $output .= "<option value='$i'>$room $i</option>";
    }
    $output .= "</select>";
    $output .= "<button id='select_room'>$choose</button>";
    return $output;
  }

  function month_buttons($lang){
    error_log($_SESSION["lang"]);
    $lang == "de" ? $prev = "Vorheriger Monat" : $prev = "Previous Month";
    $lang == "de" ? $current_month = "Aktueller Monat" : $current_month = "Current Month";
    $lang == "de" ? $next = "Nächster Monat" : $next = "Next Month";

    $output = "<button id='prev_month'>$prev</button><br>";
    $output .= "<button id='current_month'>$current_month</button><br>";
    $output .= "<button id='next_month'>$next</button>";

    return $output;
  }


}