<?php /* Smarty version Smarty-3.1.5, created on 2013-02-22 13:08:52
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/log/forgotpw_mailcontent.htm" */ ?>
<?php /*%%SmartyHeaderCode:9264853351275fd4a037e0-83628534%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '36cf6df97928d01abc65c7551772538923ecafa6' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/log/forgotpw_mailcontent.htm',
      1 => 1361493159,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9264853351275fd4a037e0-83628534',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'config' => 0,
    'logemail' => 0,
    'logkey' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_51275fd4a2477',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51275fd4a2477')) {function content_51275fd4a2477($_smarty_tpl) {?>Sali Vuz*,

Du hast also Dein Passwort im <?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
 vergessen? Das ist kein Problem!

Besuche einfach folgenden Link, um ein neues Passwort zu bestimmen:

<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/log/newpw/<?php echo $_smarty_tpl->tpl_vars['logemail']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['logkey']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['config']->value->niceurl;?>
/log/newpw/<?php echo $_smarty_tpl->tpl_vars['logemail']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['logkey']->value;?>
</a>

Solltest Du diese eMail nicht beantragt haben, bitten wir Dich, sie einfach zu ignorieren.

Mit besten Grüßen, 

die Administration // <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
"><?php echo $_smarty_tpl->tpl_vars['config']->value->niceurl;?>
</a>

* Drakisch: Guten Tag.<?php }} ?>