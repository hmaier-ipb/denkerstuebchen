<?php
/* Smarty version 3.1.34-dev-7, created on 2021-03-22 13:21:38
  from 'D:\inetpub\www\denkerstuebchen\templates\index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_605899e272dc39_73775320',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cc720a7b42f65c153c2a0d884111cd41b4ccbe7' => 
    array (
      0 => 'D:\\inetpub\\www\\denkerstuebchen\\templates\\index.html',
      1 => 1616419296,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_605899e272dc39_73775320 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="include/css/style_main.css" type="text/css">
  <link rel="stylesheet" href="include/css/order_info_style.css" type="text/css">
  <link rel="stylesheet" href="include/css/calender_container_style.css" type="text/css">
  <link rel="stylesheet" href="include/css/confirm_res_style.css" type="text/css">
  <!--<link rel="stylesheet" href="include/css/selected_dates_style.css" type="text/css">-->
  <?php echo '<script'; ?>
 src="include/js/ajax.js"><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
>document.addEventListener("DOMContentLoaded", function(){init()})<?php echo '</script'; ?>
>
  <title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
</head>
<body>
  <h3><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</h3><br>

  <header style="display: none;" id="header">
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div></div>
    <div><label>Benutzername</label>hmaier</div>
  </header>

  <div class="staff_login">
    <label>Benutzername:</label>
    <input type="text" id="user_name">
    <label>Passwort:</label>
    <input type="password" id="user_password">
    <button id="staff_login_btn">Login</button>
  </div>

  <div class="calender_container">
    <div id='room_month_year'>
      <?php echo $_smarty_tpl->tpl_vars['room_month_year']->value;?>

    </div>
    <?php echo $_smarty_tpl->tpl_vars['month_buttons']->value;?>

    <div id="calender">
      <?php echo $_smarty_tpl->tpl_vars['calender']->value;?>

    </div>
  </div>



    <div id="search_time_period">
      <label for="week_num"><?php echo $_smarty_tpl->tpl_vars['time_period_search_text']->value;?>
</label>
      <input id="week_num" class="input active-input" type="number" value="1"  step="1" min="1" max="17">
      <button id="search_period_button" class="search_btn btn"><b><?php echo $_smarty_tpl->tpl_vars['search_period_button_text']->value;?>
</b></button>
    </div>

    <div id="search_period_sug">

      <ul>
        <?php echo $_smarty_tpl->tpl_vars['pre_list']->value;?>

      </ul>

    </div>

    <div id="dates">
      <label for="start_date"><?php echo $_smarty_tpl->tpl_vars['start_date']->value;?>
</label>
      <input disabled id="start_date" class="input date-field">
      <label for="end_date"><?php echo $_smarty_tpl->tpl_vars['end_date']->value;?>
</label>
      <input disabled id="end_date" class="input date-field">
    </div>




  <div id="uid_div">
    <label for="user_search"><?php echo $_smarty_tpl->tpl_vars['user_search_prompt']->value;?>
</label>
    <input list="suggestions" id="user_search" class="input active-input">
    <datalist id="suggestions"><?php echo $_smarty_tpl->tpl_vars['suggestion_html']->value;?>
</datalist>
    <p id="search_response" style="display: block; height: 2rem;"></p>
  </div>

  <!--<p id="search_response" style="display: none;"></p>-->

  <div class="order-info">

    <input disabled placeholder="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" type="text" class="input" id="name-input">

    <input disabled placeholder="<?php echo $_smarty_tpl->tpl_vars['phone']->value;?>
" type="text"  class="input" id="phone-input">

    <input disabled type="text" class="input" id="email-input" placeholder="@ipb-halle.de">

    <input disabled class="input" id="department-input" name="department" placeholder="<?php echo $_smarty_tpl->tpl_vars['department']->value;?>
">


    <div id="status-input">
      <input type="radio" id="employee" name="status" value="employee" checked>
      <label for="employee"><?php echo $_smarty_tpl->tpl_vars['employee']->value;?>
</label>
      <br>
      <input type="radio" id="guest" name="status" value="guest">
      <label for="guest"><?php echo $_smarty_tpl->tpl_vars['guest']->value;?>
</label>
      <br>
    </div>

    <button class="send_btn btn" id="btn" tabindex="-1"><b><?php echo $_smarty_tpl->tpl_vars['button']->value;?>
</b></button>

    <div id="output"></div><br>

  </div>

  <div class="confirm_reservation">
    <button id="close_res"><b>X</b></button>
    <div class="res_list"><?php echo $_smarty_tpl->tpl_vars['res_list']->value;?>
</div>
    <div class="user_info"></div>
  </div>

</body>
</html><?php }
}
