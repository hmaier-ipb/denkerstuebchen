
function $(id){return document.getElementById(id);}
function $$ (class_name) {return document.getElementsByClassName(class_name);}

var params;
var ajaxObj;
var action;
var error_message;
var url;
var output;

var send_btn;
var firstname;
var surname;
var phone;
var email;
var department;
var status_input;
var radio_buttons;
var lang;
var img;
var selected_room;
var calender;
var month_days;


function init(){
  send_btn = $$("send_btn")[0];
  firstname = $("name-input");
  surname = $("surname-input");
  phone = $("phone-input");
  email = $("email-input");
  output = $("output");
  calender = $$("calender")[0];
  month_days = $$("current_month_days");
  action = "get-lang";
  send_info("action="+action);
  initEventListeners();
}

function validate(input,pattern){
  let reg = new RegExp(pattern);
  return reg.test(input.value);
}

function initEventListeners(){

  send_btn.addEventListener("click", function (){

    action = "form-data";
    department = $("department-input").value;
    radio_buttons = document.getElementsByName("status");
    //console.log(radio_buttons);
    for(let i = 0;i<radio_buttons.length;i++){
      if(radio_buttons[i].checked){
        status_input = radio_buttons[i].value;
      }
    }
    params =
      "action="+action+
      "&name="+firstname.value+
      "&surname="+surname.value+
      "&phone="+phone.value+
      "&email="+email.value+
      "&department="+department+
      "&status="+status_input;


    //send_info() here for debugging the email


    let error = [];
    //get terms, where an error occurred, from the webpage
    // removing the colon from the end of each string
    if(validate(firstname,/^[A-Za-z]{2,}$/)===false){
      error.push($$("name")[0].innerHTML.replace(":",""));
    }
    if(validate(surname,/^[A-Za-z]{3,}$/)===false){
      error.push($$("surname")[0].innerHTML.replace(":",""));
    }
    if(validate(phone,/^\d{4,}$/g)===false){
      error.push($$("phone")[0].innerHTML.replace(":",""));
    }
    if(validate(email,/(\w+|\w+.\w+){3,}@(ipb-halle.de)/)===false){
      error.push($$("email")[0].innerHTML.replace(":",""));
    }

    if(error.length>0){
    switch(lang){
      case "de":
        if(error.length <= 1){
          output.innerHTML = "Das Feld <b>"+error[0]+"</b> wurde falsch/nicht ausgefüllt.";
        }else{
          output.innerHTML = "Die Felder ";

        for(let i = 0;i<error.length;i++) {
          if (i === error.length - 1) {
            output.innerHTML += "and <b>" + error[i] + "</b>";
          }
          if (i === error.length - 2) {
            output.innerHTML += "<b>" + error[i] + "</b> ";
          }
          if (i < error.length - 2) {
            output.innerHTML += "<b>" + error[i] + "</b>, ";
          }
        }
        output.innerHTML += " wurden nicht/falsch ausgefüllt."

        }
        output.style.display = "block";
        break;
      case "en":
        if(error.length <= 1){
          output.innerHTML = "The field <b>"+error[0]+"</b> has been filled out incorrectly";
        }else {
          output.innerHTML = "The fields ";
          for (let i = 0; i < error.length; i++) {

            if (i === error.length - 1) {
              output.innerHTML += "and <b>" + error[i] + "</b>";
            }
            if (i === error.length - 2) {
              output.innerHTML += "<b>" + error[i] + "</b> ";
            }
            if (i < error.length - 2) {
              output.innerHTML += "<b>" + error[i] + "</b>, ";
            }
          }
          output.innerHTML += " have been filled out incorrectly."

        }

        output.style.display = "block";
        break;
      default:
        if(error.length <= 1){
          output.innerHTML = "The field <b>"+error[0]+"</b> has been filled out incorrectly";
        }else {
          output.innerHTML = "The fields ";
          for (let i = 0; i < error.length; i++) {

            if (i === error.length - 1) {
              output.innerHTML += "and <b>" + error[i] + "</b>";
            }
            if (i === error.length - 2) {
              output.innerHTML += "<b>" + error[i] + "</b> ";
            }
            if (i < error.length - 2) {
              output.innerHTML += "<b>" + error[i] + "</b>, ";
            }
          }
          output.innerHTML += " have been filled out incorrectly."

        }

        output.style.display = "block";
        break;
      }
      //changing color of send button
      output.style.color = "#FF2635";
    }else{
      switch(lang){
        case "de":
          output.innerHTML = "<u>Ihre Anfrage wurde erfolgreich versendet.</u>";
          break;
        case "eng":
          output.innerHTML = "<u>Your reservation has been send out successfully.</u>";
          break;
        default:
          output.innerHTML = "<u>Your reservation has been send out successfully.</u>";
      }
      send_info(params);
      output.style.color = "#277e34";
      img = $$("img");
      for(let i = 0;i<img.length;i++){
        img[i].style.display = "none";
      }
      output.style.display = "block";
      firstname.value = null;
      surname.value = null;
      phone.value = null;
      email.value = "@ipb-halle.de";
    }
  })


  //checking the input of the firstname on keyup
  $("name-input").addEventListener("keyup",function (){
    //name
    if(validate(firstname,/^[A-Za-z]{2,}$/)){
      $("img1").style.display = "flex";
      }else{
      $("img1").style.display = "none";
    }
  })

  //checking the input of the surname on keyup
  $("surname-input").addEventListener("keyup",function () {
    //name
    if(validate(surname,/^[A-Za-z]{3,}$/)){
      $("img2").style.display = "flex";
    }else{
      $("img2").style.display = "none";
    }
  })

 //checking the input of the phone number
  $("phone-input").addEventListener("keyup", function(){
    //phone
    if(validate(phone,/^\d{4,}$/g)){
      $("img3").style.display = "flex";
    }else{
      $("img3").style.display = "none";
    }
  })

  //checking the email input on keyup
  $("email-input").addEventListener("keyup",function (){
    //email
    if(validate(email,/(\w+|\w+.\w+){3,}@(ipb-halle.de)/)){
      $("img4").style.display = "flex";
    }else{
      $("img4").style.display = "none";
    }
  })

  //set the cursor at position 0 when focusing the email input
  $("email-input").addEventListener("focus", function (c){
    var input = $("email-input");
    if (input.setSelectionRange){
      input.focus();
      input.setSelectionRange(0,0);
    }
  })

  $("select_room").addEventListener("click", e => {
    selected_room = $("rooms").value;
    action = "room_select";
    params ="action="+action+"&room="+selected_room;
    send_info(params);

  })

  $("prev_month").addEventListener("click",e => {
    action = "prev_month";
    params = "action="+action;
    send_info(params);
  })

  $("current_month").addEventListener("click",e => {
    action = "current_month";
    params = "action="+action;
    send_info(params);
  })

  $("next_month").addEventListener("click",e => {
    action = "next_month";
    params = "action="+action;
    send_info(params);
  })

  for(var i = 0; i<month_days.length; i++){
    month_days[i].addEventListener("click", e =>{
      console.log(e.target.id);
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
  if (ajaxObj.readyState === 4) {
    //console.log(ajaxObj.responseText);
    if (ajaxObj.status === 200) {
      try {
        json_response = JSON.parse(ajaxObj.responseText);
      } catch (e) {
        //console.log("Ungültige Daten. Kein JSON String!");
        //console.log(ajaxObj.responseText);
        return false;
      }
      switch (action) {
        case "form-data":
          action = "";
          $("output").innerText = json_response[0];
          break;
        case "get-lang":
          lang = json_response;
          //console.log(lang);
          break;
        case "room_select":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          break;
        case "prev_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          break;
        case "current_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          break;
        case "next_month":
          action = "";
          calender.innerHTML = json_response; // the DIV surrounding the calender
          break;
        default:
          output.innerHTML = "INVALID ACTION";
          break;
      }

    }

  }
}