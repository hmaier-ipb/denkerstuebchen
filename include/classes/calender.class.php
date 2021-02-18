<?php


/**
 * Builds Calender
 */

//require("control_db.class.php");

class calender extends control_db
{
  public array $weekdays_array;
  public array $monate;


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


  function create_calender($current_time,$language): string
  {
    $room_number = $_SESSION["room_number"];

    $control_db = new control_db();
    $validate = new validation();
    $occupied_days = $control_db->get_occupied_dates("tr_$room_number");
    //error_log(json_encode($occupied_days));


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

    $this->weekdays_array = [
      ["Monday", "Montag"],
      ["Tuesday", "Dienstag"],
      ["Wednesday","Mittwoch"],
      ["Thursday","Donnerstag"],
      ["Friday","Freitag"],
      ["Saturday","Samstag"],
      ["Sunday","Sonntag"]
    ];
    $this->monate = [
      "Januar",
      "Februar",
      "März",
      "April",
      "Mai",
      "Juni",
      "Juli",
      "August",
      "September",
      "Oktober",
      "November",
      "Dezember"
    ];

    $used_language = [];

    if($language == "de"){
      foreach($this->weekdays_array as $day){
        $used_language[] = $day[1];
      }
    }else{
      foreach($this->weekdays_array as $day){
        $used_language[] = $day[0];
      }
    }

    $language == "de" ? $room_name = "Denkerstübchen" : $room_name = "Thinkersroom";

    $language == "de" ? $current_month = $this->monate[date("n",$current_time)-1] : $current_month = date("F",$current_time);

    //**********************
    //STRING CREATION BEGINS
    //**********************
    $calender_string = "<p id='room_month_year'><b>" . $room_name . " " . $room_number . " </b> <i> " . $current_month. " " .date("Y",$current_time) . "</i></p> "; //current month
    $calender_string .= "<table id='calender_table'>"; // calender string which contains the HTML

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
    //using control_db

    for($i=1;$i<=$days_this_month;$i++){

      //Formatting day
      $i<10?$iterated_date = "0".$i.".".$month.".".$year."":$iterated_date = "".$i.".".$month.".".$year.""; //string of iterated

      if($weekday_count == 7){
        $weekday_count = 0;
        $calender_string .= "</tr>";//close a row
        $calender_string .= "<tr>"; //open a row
      }


       if($validate->is_occupied($iterated_date,$occupied_days) == true){
        //red colored, class "occupied"
         $color = "#FF2635";
         $calender_string .= "<td class='current_month occupied' id='$iterated_date' style='background-color: $color'>$i</td>";
      }else{
        //green colored, class "free"
         $color = "#12B323";
         $calender_string .= "<td class='current_month free' id='$iterated_date' style='background-color: $color'>$i</td>";
      }
      $weekday_count += 1;
//      $color = "#12B323";
//      $calender_string .= "<td class='current_month free' id='$iterated_date' style='background-color: $color'>$i</td>";
      //table cells

    }

    //NEXT MONTH
    if($last_weekday_month !== 7){
      $days_next_month = 1; // helper-var to set days of next month
      for($i = $last_weekday_month;$i<7;$i++){
        $calender_string .= "<td class='next_month' style='background-color: #666666'>$days_next_month</td>";//table cells
        $days_next_month +=1;
      }
      $calender_string .= "</tr>";//closing the last row
    }
    $calender_string .= "</table>";//closing the table

    $calender_string .= $this->month_buttons($language);//append the month buttons to the calender

    return $calender_string."<br>";
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