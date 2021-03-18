<?php

//require_once("easy_pdo_db.class.php");
require_once("validation.class.php");

class control_db extends easy_pdo_db
{
  function __construct()
  {
    parent::__construct();
    $this->validate = new validation();
  }

  //MAIN FUNCTION
  function new_reservation()
  {
    //$response = [];
    $full_name = $_POST["full_name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $department = $_POST["department"];
    $status = $_POST["status"];

    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $room_number = $_POST["room_number"];

    $cols_user = "full_name,phone,email,department,status";
    $vars_array_user = [$full_name, $phone, $email, $department, $status];

    $user_id = $this->is_user_existing($email, $cols_user, $vars_array_user);
    $reservation_status = $this->has_user_id_reservations($user_id);

    if ($reservation_status[0] !== 0) {//if not "no reservation"
      $response = $reservation_status;
    } else {
      $response = $this->make_reservation($user_id, $start_date, $end_date, $room_number);
    }

    return $response;
  }

  function has_email_reservation($email){
    if ($this->does_exist("user", "email", $email) !== 0) {
      $user_id = $this->get_id("user", "email", $email);
      return $this->has_user_id_reservations($user_id);
    }else{
      return false;
    }
  }//manchmal kommt "false" und manchmal  "[0]"

  function is_user_existing($email, $cols_user, $vars_array_user)
  {
    if ($this->does_exist("user", "email", $email) == 0) {
      $this->insert_into("user", $cols_user, $vars_array_user);
    }
    return $this->get_id("user", "email", $email);
  }

  function has_user_id_reservations($user_id)
  {
    $user_data = [];
    for ($i = 1; $i <= 5; $i++) {
      if ($this->does_exist("tr_$i", "user_id", $user_id) == 1) {
        $user_data[] = "existing_reservation";
        $user_data[] = $this->get_row_by_id("tr_$i", $user_id);
        $user_data[] = $i;
        // user_data ARRAY = [id,[id,user_id,start_date,end_date],room_number]
        return $user_data; //already a reservation
      }
    }
    return [0];//no reservation
  }

  function make_reservation($user_id, $start_date, $end_date, $room_number)
  {
    $cols_string = "user_id, start_date, end_date";
    $vars = [$user_id, $start_date, $end_date];
    $this->insert_into("tr_$room_number", $cols_string, $vars);
    $response = ["success"];

    return $response;
  }

  function get_occupied_dates()
  {
    $occupied_dates = [];
    $num_tr = $this->count_num_tr();
    //get all occupation-periods from all thinkers-room tables
    for ($r = 1; $r <= $num_tr; $r++) {
      $num_rows = $this->count_rows("tr_$r");
      //error_log($num_rows);
      $tr_dates = [];
      if($num_rows !== 0)
      {
        for ($x = 1; $x <= $num_rows; $x++)
        {
          $row = $this->get_row_by_id("tr_$r", $x);
          $tr_dates[] = [$row["start_date"], $row["end_date"]];
        }
      }else{
        $tr_dates = [[0, 0]];
      }

      $occupied_dates[] = $tr_dates;
    }
//    error_log("from get occupied dates();");
//    error_log(json_encode($occupied_dates));
    return $occupied_dates;
  }

  function count_num_tr()
  {
    $tables = $this->get_tables();
    $count = count($tables);
    //error_log($count);
    return $count - 1;// all "tr_x" minus "user"
  }

  function search_free_room($start_date, $end_date)
  {
    $validate = new validation();
    $occupied_dates = $this->get_occupied_dates();
    $tr_num = $this->count_num_tr();
    $free_rooms = [];

    for($tr = 1; $tr<=$tr_num;$tr++){
      $tr_dates = $occupied_dates[$tr-1];
      if($validate->is_period_available($start_date,$end_date,$tr_dates) == false){
       $free_rooms[] = false;
      }else{
        $free_rooms[] = [$tr];
      }
      //if room does not encloses/overlaps reservation, add room_num to free_rooms
    }
    //error_log(json_encode($free_rooms));
    return $free_rooms;//returning free rooms for this period
  }

  function delete_old_reservations(){
    $all_dates = $this->get_occupied_dates();
    $room_num = 0;
    $today_ts = strtotime("00:00:00",time());
    $today_date = date("d.m.Y",$today_ts);

    foreach($all_dates as $room){
      $room_num++;
      for($i = 1;$i<=count($room);$i++){
        $start_date = $room[$i-1][0];
        //$end_date = $room[$i-1][1];
        if(strtotime($start_date)<$today_ts && $start_date !== 0){// updating the start_date in the database
          $this->update_row("tr_$room_num",["start_date"],[$today_date],$i);
        }
        $row =  $this->get_row_by_id("tr_$room_num",$i);
        if($row !== null){
          $start_date = $row["start_date"];
          $end_date = $row["end_date"];
          if(strtotime($end_date)<strtotime($start_date)){// deleting then date if reservation period is over
            $this->delete_row("tr_$room_num",$i);
          }
        }
      }
    }
  }

  function next_free_period($weeks){
    $response = "<ul id='periods_list'>";

    $all_dates = $this->get_occupied_dates();
    $today_ts = strtotime("00:00:00",time());
    $today_date = date("d.m.Y",time());

    //error_log(json_encode($all_dates));
    $room_text = $_SESSION["lang"] == "de" ? "Raum:" : "Room:";

    foreach($all_dates as $index => $room){
      //error_log(json_encode($room));
      $room_number = $index+1;
      if($room[0][0] !== 0 && $room[0][1] !== 0){// wenn es belegungen gibt

        //foreach through ONE ROOM
        foreach($room as $x => $res){ // überprüfe die zeit zwischen zwei reservierungen in einem raum
          error_log(json_encode($res));
          error_log(json_encode(count($room)));
          $end_date = $res[1];
          if($x !== count($room)-1){
            $next_start_date = $room[$x+1][0]; //next start date
            $ts_end = strtotime($end_date); //current iterations end date
            $ts_next_start = strtotime($next_start_date);

            $weeks_ts = strtotime("+$weeks Week",$ts_end);

            $new_start_date = date("d.m.Y",$ts_end+86400); //last end date plus one day -> 86400 Seconds/days
            $new_end_date = date("d.m.Y",$weeks_ts);
            if($weeks_ts < $ts_next_start){
              $response .= "<li class='res_sug'><b>$room_text $room_number</b> $new_start_date - $new_end_date</li>";
              break; //take earliest available period
            }

          }else{
            $ts_end = strtotime($end_date);
            $new_start_date = date("d.m.Y",$ts_end+86400);
            $weeks_ts = strtotime("+$weeks Week",$ts_end);
            $new_end_date = date("d.m.Y",$weeks_ts);
            $response .= "<li class='res_sug'><b>$room_text $room_number</b> $new_start_date - $new_end_date</li>";
          }

        }//end foreach through ONE ROOM take next room

      }else{// called when no reservations are placed -> ["0","0"]
        //from today plus $weeks
        $weeks_ts = strtotime("+$weeks Week",$today_ts);
        $date_x_weeks = date("d.m.Y",$weeks_ts);
        $response .= "<li class='res_sug'><b>$room_text $room_number</b> $today_date - $date_x_weeks</li>";
      }
    }
    $response .= "</ul>";
    return $response;
  }


}

