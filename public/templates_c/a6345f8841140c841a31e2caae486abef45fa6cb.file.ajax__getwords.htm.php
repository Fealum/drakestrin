<?php /* Smarty version Smarty-3.1.5, created on 2013-04-20 16:05:59
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/dictionary/ajax__getwords.htm" */ ?>
<?php /*%%SmartyHeaderCode:4391334575171a5ccb15ec7-63157864%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6345f8841140c841a31e2caae486abef45fa6cb' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/dictionary/ajax__getwords.htm',
      1 => 1366466718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4391334575171a5ccb15ec7-63157864',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5171a5ccb658e',
  'variables' => 
  array (
    'obj' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5171a5ccb658e')) {function content_5171a5ccb658e($_smarty_tpl) {?>[
<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['i']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['i']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
 $_smarty_tpl->tpl_vars['i']->iteration++;
 $_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration === $_smarty_tpl->tpl_vars['i']->total;
?>	{
		"id": "<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
",
		"word": "<?php echo $_smarty_tpl->tpl_vars['i']->value->word;?>
", 
		"wordtype": "<?php echo $_smarty_tpl->tpl_vars['i']->value->wordtype->code;?>
",
		"language": "<?php echo $_smarty_tpl->tpl_vars['i']->value->language;?>
"
	}<?php if (!$_smarty_tpl->tpl_vars['i']->last){?>,<?php }?>
<?php } ?>
]<?php }} ?>