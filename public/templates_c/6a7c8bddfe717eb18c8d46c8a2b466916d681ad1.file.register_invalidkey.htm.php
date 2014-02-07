<?php /* Smarty version Smarty-3.1.5, created on 2013-05-22 01:51:08
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_notice/register_invalidkey.htm" */ ?>
<?php /*%%SmartyHeaderCode:230319385519c086c707b12-36684414%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a7c8bddfe717eb18c8d46c8a2b466916d681ad1' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_notice/register_invalidkey.htm',
      1 => 1361493167,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '230319385519c086c707b12-36684414',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'registerkey' => 0,
    'registeremail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_519c086c7cfdb',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_519c086c7cfdb')) {function content_519c086c7cfdb($_smarty_tpl) {?>Der Best&auml;tigungsschl&uuml;ssel <em><?php echo $_smarty_tpl->tpl_vars['registerkey']->value;?>
</em> passt leider nicht zur eMail-Adresse <em><?php echo $_smarty_tpl->tpl_vars['registeremail']->value;?>
</em>!<?php }} ?>