<?php /* Smarty version Smarty-3.1.5, created on 2013-02-24 23:22:37
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_notice/register_emailalreadyinuse.htm" */ ?>
<?php /*%%SmartyHeaderCode:788892867512a92ad798a17-95137498%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '67272e656db8756b482ca763a0f3852df115ea1c' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_notice/register_emailalreadyinuse.htm',
      1 => 1361493167,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '788892867512a92ad798a17-95137498',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'registeremail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_512a92ad7e160',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_512a92ad7e160')) {function content_512a92ad7e160($_smarty_tpl) {?>Die eMail-Adresse <em><?php echo $_smarty_tpl->tpl_vars['registeremail']->value;?>
</em> wird bereits f&uuml;r ein Nutzerkonto verwendet!<?php }} ?>