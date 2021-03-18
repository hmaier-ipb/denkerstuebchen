
function $(id){return document.getElementById(id);}
function $$ (class_name) {return document.getElementsByClassName(class_name);}

var params;
var ajaxObj;
var action;
var output;
var json_response;


var send_btn;
var full_name;
var phone;
var email;
var department;
var status_input;
var radio_buttons;
var lang;
var img;
//var selected_room;
var calender;
var start_date;
var end_date;
var room_selection;
var room_number;
var room_month_year;
var user_search;
var uid;
var user_data;
//var date_search;
var start_date_search;
var end_date_search;
var search_response;
var search_period_output;
var search_period_button;
var week_num;
var res_sug;
var start_sug;
var end_sug;
var dates;

function init(){
  send_btn = $$("send_btn");
  full_name = $("name-input");
  phone = $("phone-input");
  email = $("email-input");
  department = $("department-input");
  output = $("output");
  calender = $("calender");
  start_date = $("start_date");
  end_date = $("end_date");
  start_date.value = "";
  end_date.value = "";
  //room_selection = $("room_selection");
  room_month_year = $("room_month_year");
  user_search = $("user_search");
  //date_search = $("date_search");
  start_date_search = $("start_date_search");
  end_date_search = $("end_date_search");
  search_response = $("search_response");
  search_period_output = $("search_period_output");
  search_period_button = $("search_period_button");
  week_num = $("week_num");
  res_sug = $$("res_sug"); //reservation suggestion

  // action = "get-lang";
  // send_info("action="+action);
  initEventListeners();
  //console.log(Date.now())
}

function initEventListeners(){

  search_period_button.addEventListener("click", e =>{
    if(week_num.value >= 1 && week_num.value <= 17){
      action = "period_search";
      params = "action="+action+"&week_num="+week_num.value;
      send_info(params);
    }else{
      search_period_output.innerHTML = "";
    }
  })

  user_search.addEventListener("input", e =>{
    action = "user_search";
    uid = user_search.value;
    params = "action="+action+"&uid="+uid;
    send_info(params);
    //console.log(uid);
    //console.log(params);
    })

  send_btn[0].addEventListener("click", function (){
    action = "form-data";
    //room_number = room_selection.options[room_selection.selectedIndex].value;
    // room_number gets set by ajax
    radio_buttons = document.getElementsByName("status");
    for(let i = 0;i<radio_buttons.length;i++){if(radio_buttons[i].checked){status_input = radio_buttons[i].value;}}
    params =
      "action="+action+
      "&full_name="+full_name.value+
      "&phone="+phone.value+
      "&email="+email.value+
      "&department="+department.value+
      "&status="+status_input+
      "&start_date="+start_date.value+
      "&end_date="+end_date.value+
      "&room_number="+room_number;

    //****************************
    //SENDING OUT THE RESERVATION
    //****************************

    send_info(params);
    // console.log("form data send to php");
    // console.log(params);
    })
  //td_listener();
  //room_selection_listener();
  month_buttons_listener();
}

// function td_listener(){ // EVENT LISTENER FOR SINGLE TABLE CELLS
//   for(var i = 0;i<$$("current_month").length;i++){ //current_month -> table cells this month
//     $$("current_month")[i].addEventListener("click", set_dates)
//   }
// }

function month_buttons_listener(){
  $("prev_month").addEventListener("click",e => {
    action = "prev_month";
    params = "action="+action;
    send_info(params);
    //console.log("prev_month pressed")
  })

  $("next_month").addEventListener("click",e => {
    action = "next_month";
    params = "action="+action;
    send_info(params);
    //console.log("next_month pressed")
  })
}

// function date_input_listeners(){
//
//   start_date_search.addEventListener("keyup", e =>{
//     //console.log(e.target.value)
//     //console.log(get_date(e.target.value))
//     start_date = e.target.value;
//     if(get_date(e.target.value)){
//       action = "date_search"
//       params = "action="+action+"&start_date="+e.target.value
//       send_info(params)
//       console.log(params)
//     }else{
//       end_date_search.disabled = true;
//     }
//   })
//
//   end_date_search.addEventListener("keyup",e =>{
//     //console.log(get_date(e.target.value))
//     search_response.style.display = "none";
//
//     end_date = e.target.value;
//     if(get_date(e.target.value)){
//       action = "date_search"
//       params = "action="+action+"&end_date="+end_date
//       send_info(params)
//       console.log(params)
//     }
//
//   })
//
// }

function validate(input,pattern){
  let reg = new RegExp(pattern);
  return reg[Symbol.match](input);
}

function get_date(input){
  //var date_pattern =  /(\d|\d{2})\.(\d|\d{2})\.(\d{4}|\d{2})/g
  //console.log(input)
  var pattern =  /(\d{2})\.(\d{2})\.(\d{4})/g
  var regex = validate(input,pattern)
  //console.log(regex)
  if(regex !== null){
    return regex;
  }
}


// function load_calender_listeners(){
//   td_listener();
// }
//
//
// function set_dates(e){
//   // console.log(start_date);
//   // console.log(end_date);
//   if( start_date.value !== "" &&  end_date.value !== ""){ //when both vars are defined
//     start_date.value = "";
//     end_date.value = "";
//   }
//   if(start_date.value === ""){
//     start_date.value = e.target.id;
//   }else{
//     end_date.value = e.target.id;
//   }
//   action = "submit_dates";
//   params = "action="+action+"&start_date="+start_date.value+"&end_date="+end_date.value;
//   send_info(params);//is start_date > end_date?
//   console.log(params)
// }

function reset_input_values(){
  img = $$("img");
  for(let i = 0;i<img.length;i++){img[i].style.display = "none";} // disappearing green checks
  full_name.value = "";
  phone.value = "";
  email.value = "@ipb-halle.de";
  //resetting
  start_date.value = "";
  end_date.value = "";

}

function init_list_listeners(){
  //console.log(res_sug.length)
  for(var x = 0; x<res_sug.length;x++){
    res_sug[x].addEventListener("click", e => {
      dates = get_date(e.target.innerHTML);
      start_sug = dates[0];
      end_sug = dates[1];
      // console.log(start_sug)
      // console.log(end_sug)
      start_date.value = start_sug;
      end_date.value = end_sug;
    })
  }
}



function getAjaxObject() {
  //creating an ajax object
  if (window.ActiveXObject)
    return new ActiveXObject("Microsoft.XMLHTTP");
  else if (window.XMLHttpRequest)
    return new XMLHttpRequest();
  else {
    return null;
  }
}
function send_info(parameters) {
  //sending parameters(data) to url(index.php)
  ajaxObj = getAjaxObject();
  if (ajaxObj !== null) {
    ajaxObj.open("POST", "index.php", true);
    ajaxObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxObj.onreadystatechange = setOutput;
    ajaxObj.send(parameters);
  }
  else {
    console.log("Kein Ajax Objekt.");
    return false;
  }
}

function setOutput() {
  //receiving output form php as json
  if (ajaxObj.readyState !== 4) {
    return;
  }
  if (ajaxObj.status !== 200) {
    return;
  }
  try {
    json_response = JSON.parse(ajaxObj.responseText);
  } catch (e) {
    console.log("Ungültige Daten. Kein JSON String!");
    console.log(ajaxObj.responseText);
    return false;
  }
  switch (action) {
    case "form-data":
      //console.log("form-data received from php")
      //console.log(json_response);
      action = "";

      if (json_response[0][1] !== true) {// validation found errors
        output.style.color = "#DF171E";
        $("output").innerHTML = json_response[0][0];
      } else {                        // validation found no errors
        reset_input_values();
        output.style.color = "#277e34";
        $("output").innerHTML = json_response[0][0];
        calender.innerHTML = json_response[1];
        //load_calender_listeners();
        //room_selection_listener();
      }

      output.style.display = "block";
      break;
    // case "get-lang":
    //   //lang = json_response;
    //   //console.log(lang);
    //   break;
    // case "room_select":
    //   action = "";
    //   room_month_year.innerHTML = json_response[0]
    //   calender.innerHTML = json_response[1]; // the DIV surrounding the calender
    //   //load_calender_listeners();
    //   //room_selection_listener();
    //   break;
    case "prev_month":
      action = "";
      room_month_year.innerHTML = json_response[0];
      calender.innerHTML = json_response[1]; // the DIV surrounding the calender
      //load_calender_listeners();
      break;
    /* case "current_month":
       action = "";
       calender.innerHTML = json_response; // the DIV surrounding the calender
       load_calender_listeners();
       break;*/
    case "next_month":
      action = "";
      room_month_year.innerHTML = json_response[0];
      calender.innerHTML = json_response[1]; // the DIV surrounding the calender
      //load_calender_listeners();
      break;
    case "submit_dates":
      action = "";
      //console.log(json_response)
      if (json_response === 0) {
        output.style.color = "#277e34";
        output.innerHTML = "";
      } else { // an error has occurred
        output.style.color = "#DF171E";
        start_date.value = "";
        end_date.value = "";
        output.innerHTML = json_response
      }
      //load_calender_listeners();
      break;
    case "user_search":
      //$("suggestions").innerHTML = json_response[0]
        //console.log(json_response);

      if(json_response[5] === "existing_reservation"){
        search_response.style.display = "block";
        search_response.innerHTML = json_response[6];
      }else{
        user_data = json_response;
        //console.log(json_response)
        search_response.innerHTML = "";
        if (user_data.length !== 0) {
          full_name.value = user_data[0];
          phone.value = user_data[2];
          email.value = user_data[3];
          department.value = user_data[4];
        } else {
          full_name.value = null;
          phone.value = null;
          email.value = null;
          department.value = null;
        }

      }
      break;
    // case "date_search":
    //   //console.log(json_response);
    //   switch (json_response[0]){
    //     case "enable":
    //       end_date_search.disabled = false;
    //       end_date_search.focus();
    //       break;
    //     case "do_not_enable":
    //       end_date_search.disabled = true;
    //       break;
    //     case "end_earlier_start":
    //       search_response.style.display = "block";
    //       search_response.innerHTML = json_response[1];
    //       break;
    //     case "period_greater_than_four_months":
    //       search_response.style.display = "block";
    //       search_response.innerHTML = json_response[1];
    //       break;
    //     case "free_room":
    //       user_search.disabled = false;
    //       user_search.focus();
    //       console.log(json_response[1][0])
    //       room_number = json_response[1]
    //       break;
    //     case "no_free_room":
    //       search_response.style.display = "block";
    //       search_response.innerHTML = json_response[1];
    //       break;
    //     default:
    //       break;
    //   }
    case "period_search":
      search_period_output.innerHTML = json_response[0];
      init_list_listeners();
      break;

    default:
      output.innerHTML = "INVALID ACTION";
      break;
  }
}