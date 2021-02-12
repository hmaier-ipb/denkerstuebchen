<?php
/* Smarty version 3.1.34-dev-7, created on 2021-02-12 09:25:40
  from 'D:\inetpub\www\denkerstuebchen\templates\index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_602649943387f6_72752651',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cc720a7b42f65c153c2a0d884111cd41b4ccbe7' => 
    array (
      0 => 'D:\\inetpub\\www\\denkerstuebchen\\templates\\index.html',
      1 => 1613121939,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_602649943387f6_72752651 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="include/css/style.css" type="text/css">
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
  <div class="instruction"><?php echo $_smarty_tpl->tpl_vars['instruction']->value;?>
</div>

  <div class="calender_container">
    <div id="calender"><?php echo $_smarty_tpl->tpl_vars['calender']->value;?>
</div>
    <div id="calender_option"><?php echo $_smarty_tpl->tpl_vars['room_select']->value;?>
<br><br><?php echo $_smarty_tpl->tpl_vars['month_buttons']->value;?>
</div>
  </div>

  <div class="order-info">

    <span class="name"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
:</span> <input placeholder="<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" type="text" class="input" id="name-input">
    <img class="img" style="display: none;" id="img1" src="include/media/green_check.png" alt="no pic">

    <span class="surname"><?php echo $_smarty_tpl->tpl_vars['surname']->value;?>
:</span> <input placeholder="<?php echo $_smarty_tpl->tpl_vars['surname']->value;?>
"  type="text"  class="input" id="surname-input">
    <img class="img" style="display: none;" id="img2" src="include/media/green_check.png" alt="no pic">

    <span class="phone"><?php echo $_smarty_tpl->tpl_vars['phone']->value;?>
:</span> <input placeholder="<?php echo $_smarty_tpl->tpl_vars['phone']->value;?>
" type="text"  class="input" id="phone-input">
    <img class="img" style="display: none;" id="img3" src="include/media/green_check.png" alt="no pic">

    <span class="email"><?php echo $_smarty_tpl->tpl_vars['email']->value;?>
:</span> <input type="text" class="input" id="email-input" value="@ipb-halle.de">
    <img class="img" style="display: none;" id="img4" src="include/media/green_check.png" alt="no pic">

    <span class="dep"><?php echo $_smarty_tpl->tpl_vars['department']->value;?>
:</span>

    <select id="department-input" name="department">
      <?php echo $_smarty_tpl->tpl_vars['departments']->value;?>

    </select>

    <span class="status">Status:</span>

    <button class="send_btn" id="btn"><b><?php echo $_smarty_tpl->tpl_vars['button']->value;?>
</b></button>

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
    <div class="selected_dates">
      <div id="start_date"></div><br>
      <div id="end_date"></div><br>
      <div id="date_error"></div>
    </div>

    <div id="output" style="display:none"><?php echo $_smarty_tpl->tpl_vars['output']->value;?>
</div><br>


  </div>
</body>
</html><?php }
}
