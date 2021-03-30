<?php


//generates html for confirm_reservation_div

class res_admin extends control_db
{
  function html_res_list(){
    $res_string = "";
    $num_tr = $this->count_num_tr();
    $dates = $this->get_occupied_dates();
    //error_log(json_encode($dates));
    for($room_num=1; $room_num<=$num_tr;$room_num++){

      foreach($dates[$room_num-1] as $res){
        if($res[4] == 0 && $res[2] !== 0 && $res[3] !== 0){// if the reservation is not confirmed
          $user_id = $res[1];
          $start = $res[2];
          $end = $res[3];
          $row = $this->where_col_equals_var("user","id",$user_id);
          //error_log(json_encode($row));
          $full_name = $row[0]["full_name"];
          $phone = $row[0]["phone"];
          $email = $row[0]["email"];
          $department = $row[0]["department"];
          $status = $row[0]["status"];
          $_SESSION["_$user_id"] = [$user_id,$full_name,$phone,$email,$department,$status,$room_num,$start,$end];
          $res_string .= "<p class='un_confirmed_reservations'><span style='display: none;' class='user_id_db'>$user_id</span> $full_name <b>$res[2] - $res[3]</b> </p>";
        }
      }
    }
    return $res_string;
  }

  function accept_reservation($user_id,$room_number)
  {
    $rn = $room_number;
    $id = $this->get_id("tr_$rn","user_id",$user_id);
    $this->update_row("tr_$rn",["res_status"],[1],$id);
  }

  function decline_reservation($user_id,$room_number)
  {
    $rn = $room_number;
    $id = $this->get_id("tr_$rn","user_id",$user_id);
    $this->delete_row("tr_$rn",$id);
    $this->delete_row("user",$user_id);
    $this->set_autoincrement("tr_$rn");
    $this->set_autoincrement("user");
  }


}
