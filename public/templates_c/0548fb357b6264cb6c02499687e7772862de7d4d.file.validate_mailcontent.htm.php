<?php /* Smarty version Smarty-3.1.5, created on 2013-02-23 19:09:49
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/register/validate_mailcontent.htm" */ ?>
<?php /*%%SmartyHeaderCode:712427985512905ed98c028-13574308%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0548fb357b6264cb6c02499687e7772862de7d4d' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/register/validate_mailcontent.htm',
      1 => 1361493160,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '712427985512905ed98c028-13574308',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'config' => 0,
    'registeremail' => 0,
    'registerkey' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_512905ed9b5d6',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_512905ed9b5d6')) {function content_512905ed9b5d6($_smarty_tpl) {?>Sali Vuz*,

wir freuen uns, dass Du Dich entschieden hast, Dich im <?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
 zu registrieren! 

Um die Registrierung zu beginnen, ist es zuerst notwendig, Deine eMail-Adresse zu bestätigen. Hierzu bitten wir Dich, folgenden Link zu besuchen:

<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/register/registration/<?php echo $_smarty_tpl->tpl_vars['registeremail']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['registerkey']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['config']->value->niceurl;?>
/register/registration/<?php echo $_smarty_tpl->tpl_vars['registeremail']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['registerkey']->value;?>
</a>

Der Link ist <?php echo $_smarty_tpl->tpl_vars['config']->value->emailvalidfor/3600;?>
 Stunden gültig. Aber keine Sorge; sollte er verfallen, kannst Du jederzeit einen neuen beantragen.

Solltest Du diese eMail nicht beantragt haben, bitten wir Dich, sie einfach zu ignorieren &ndash; es sei denn, Du hast nun Blut geleckt und willst Dich dennoch im <?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
 registrieren.

Mit besten Grüßen, 

die Administration // <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
"><?php echo $_smarty_tpl->tpl_vars['config']->value->niceurl;?>
</a>

* Drakisch: Guten Tag.<?php }} ?>