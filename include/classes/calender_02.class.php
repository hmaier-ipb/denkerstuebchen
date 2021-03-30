<?php


class calender_02 extends control_db
{
  private int $num_tr;
  protected validation $validate;
  private control_db $control_db;


  public function __construct()
  {
    parent::__construct();
    $this->validate = new validation();
    $this->control_db = new control_db();
    $this->num_tr = $this->count_num_tr();//number of thinkers-rooms


  }

  public function create_calender($current_time)
  {//current time is $_SESSION["global_current_time"]

    // DECLARE VARIABLES
    $days_this_month = date("t", $current_time);
    $month = date("m", $current_time);
    $year = date("Y", $current_time);

    //MAIN LOOP
    $calender_string = "<table id='calender_table'>";

    //$calender_string .= "<th></th>";

    //WEEKDAYS HEADER
    for($z = 1; $z<= $days_this_month;$z++){
      $date = strtotime("$z.$month.$year");
      $weekday = date("D", $date);
      $calender_string .= "<th>$weekday</th>";
    }

    $occupied_days = $this->control_db->get_occupied_dates();// get occupied dates for the iterated thinkers-room

    //$_SESSION["lang"] == "de" ? $tr_name = "[DENKERSTÜBCHEN]" : $tr_name = "[THINKER'S-ROOM]";

    $calender_string .= "<tr>";
    //$calender_string .= "<td style='background-color: #45928e;'><b>$tr_name</b></td>";

    for($y = 1; $y <= $days_this_month;$y++){
      $color = "#CACACA";
      $calender_string .= "<td style='background-color: $color;'><b>$y</b></td>";
    }
    $calender_string .= "</tr>";

    //ROW PER ROOM
    for ($x = 1; $x <= $this->num_tr; $x++) {

      $calender_string .= "<tr>"; //open a row for a thinkers-room
      //$occupied_days = $all_occupied_days[$x-1];
      //error_log(json_encode($occupied_days[$x-1]));
      //$_SESSION["lang"] == "de" ? $room_name = "Denkerstübchen" : $room_name = "Thinkersroom";

      //$calender_string .= "<td style='background-color: #45928e;'><b>[$x]</b></td>";
//            error_log(json_encode("THINKERS-ROOM $x"));

      //TABLE CELLS FOR CELLS DAYS THIS MONTH
      for ($i = 1; $i <= $days_this_month; $i++) {
        $date = strtotime("$i.$month.$year");
        $weekday = date("D", $date);
        $i < 10 ? $iterated_date = "0" . $i . "." . $month . "." . $year . "" : $iterated_date = "" . $i . "." . $month . "." . $year . ""; //string of iterated date
        //error_log(json_encode($occupied_days[$x-1]));
        // $x-1 is the index for occupied dates for a thinkers-room
        $date_status = $this->validate->is_date_occupied($iterated_date, $occupied_days[$x - 1]);
        if ($date_status !== false) {
          //red colored, class "occupied"
          if($weekday == "Sat" || $weekday == "Sun"){
            $color = "#7A7A7A";
          }else{
            switch($date_status){
              case 0:
                $color = "#EEE409"; //yellow
                break;
              case 1:
                $color = "#FF2635";//red
                break;
              default:
                $color = "#EEE409";
                break;
            }

          }
          $calender_string .= "<td class='day_cell occupied tr_$x' id='$iterated_date' style='background-color: $color;'>$i</td>";
        } else {
          //green colored, class "free"
            if($weekday == "Sat" || $weekday == "Sun"){
              $color = "#7A7A7A";
            }else{
              //is date in the past?
              if(strtotime($iterated_date) >= strtotime("00:00:00",time())){
                $color = "#EEEEEE";
              }else{
                $color = "#AAAAAA";
              }

            }
//          $calender_string .= "<td class='day_cell free tr_$x' id='$iterated_date' style='background-color: $color;'>$i</td>";
            $calender_string .= "<td class='day_cell free tr_$x' id='$iterated_date' style='background-color: $color;'></td>";
        }
      }

      $calender_string .= "</tr>";//close a row for a thinkers-room

    }
    $calender_string .= "</table>";

    return $calender_string;
  }

  function room_month_year($current_time)
  {
    $language = $_SESSION["lang"];
    $monate = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
    $language == "de" ? $current_month = $monate[date("n", $current_time) - 1] : $current_month = date("F", $current_time);
    return "<p><b>" . $current_month . " " . date("Y", $current_time) . "</b></p> "; //current month
  }

  function month_buttons()
  {
    //$lang == "de" ? $prev = "Vorheriger Monat" : $prev = "Previous Month";
    //$lang == "de" ? $current_month = "Aktueller Monat" : $current_month = "Current Month";
    //$lang == "de" ? $next = "Nächster Monat" : $next = "Next Month";

    $prev = "&larr;";
    $next = "&rarr;";

    $output = "<button id='prev_month' class='btn calender_btn' tabindex='-1'>$prev</button><br>";
    //$output .= "<button id='current_month' class='btn calender_btn'>$current_month</button><br>";
    $output .= "<button id='next_month' class='btn calender_btn' tabindex='-1'>$next</button>";

    return $output;
  }


}