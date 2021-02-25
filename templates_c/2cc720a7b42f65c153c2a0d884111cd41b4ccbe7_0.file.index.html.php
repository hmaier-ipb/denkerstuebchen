<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-25 13:01:51
  from 'D:\inetpub\www\denkerstuebchen\templates\index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_60379fbf2b8271_26024090',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cc720a7b42f65c153c2a0d884111cd41b4ccbe7' => 
    array (
      0 => 'D:\\inetpub\\www\\denkerstuebchen\\templates\\index.html',
      1 => 1614258049,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_60379fbf2b8271_26024090 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="include/css/style_main.css" type="text/css">
  <link rel="stylesheet" href="include/css/order_info_style.css" type="text/css">
  <link rel="stylesheet" href="include/css/calender_container_style.css" type="text/css">
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


  <?php echo $_smarty_tpl->tpl_vars['room_selection']->value;?>


  <div class="calender_container">

    <div id='room_month_year'>
      <?php echo $_smarty_tpl->tpl_vars['room_month_year']->value;?>

    </div>
    <?php echo $_smarty_tpl->tpl_vars['month_buttons']->value;?>

    <div id="calender">
      <?php echo $_smarty_tpl->tpl_vars['calender']->value;?>

    </div>

  </div>
  <br>
  <div id="uid_div">
  <label for="user_search"><?php echo $_smarty_tpl->tpl_vars['search_prompt']->value;?>
</label>
  <input list="suggestions" id="user_search" class="input">
  <datalist id="suggestions"><?php echo $_smarty_tpl->tpl_vars['suggestion_html']->value;?>
</datalist>
  </div>
  <br>

  <div class="order-info">

    <div id="time">
      <?php echo $_smarty_tpl->tpl_vars['start_date']->value;?>
<input value="" class="input" id="start_date" placeholder="dd.mm.yyyy">
      <?php echo $_smarty_tpl->tpl_vars['end_date']->value;?>
<input value=""  class="input" id="end_date" placeholder="dd.mm.yyyy">
    </div>

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

    <button class="send_btn btn" id="btn"><b><?php echo $_smarty_tpl->tpl_vars['button']->value;?>
</b></button>

    <div id="output" style="display:none"><?php echo $_smarty_tpl->tpl_vars['output']->value;?>
</div><br>


  </div>
</body>
</html><?php }
}
