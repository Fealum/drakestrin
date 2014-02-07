<?php /* Smarty version Smarty-3.1.5, created on 2013-04-15 20:02:50
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/update/std.htm" */ ?>
<?php /*%%SmartyHeaderCode:18816579845126cd228f8397-27075505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '479a2cf5b212eddb3b0c2028258e1c3702cc36ea' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/update/std.htm',
      1 => 1361493162,
      2 => 'file',
    ),
    'e757fb0429ab7f4d3c82a82accfeb5318b5625be' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_.htm',
      1 => 1364755495,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18816579845126cd228f8397-27075505',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5126cd229e672',
  'variables' => 
  array (
    'config' => 0,
    'user' => 1,
    'notice' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5126cd229e672')) {function content_5126cd229e672($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: Update</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_css/_.css" />
<link rel="alternate" type="application/rss+xml" title="<?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
 RSS-Feed" href="rss.php" />
<link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_img/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_js/jquery.min.js"></script>
</head>
<body>
	<div class="bg"></div>
	<div class="schattenlinks"></div>
	<div class="schattenrechts"></div>
	<div class="blendout"></div>
	<div class="borduere"></div>
	<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a> &raquo; Update</p>
	<ul>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/index">Index</a></li>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia">Kompendium</a></li>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board">Forum</a></li>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/search">Suche</a></li>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user">Mitglieder</a></li> 
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/territory">Lehen</a></li>
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/calendar">Kalendarium</a></li> 
		<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/dictionary">Diktionar</a></li>
	</ul>
	<div id="welcome">
<?php if (isset($_smarty_tpl->tpl_vars['user']->value)){?>
	<a id="notifypic" href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_img/notifications_out.png" height="25" width="36" alt="Keine neuen Nachrichten" /></a>
	&raquo; Sali Vuz, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value->name;?>
</a>! [<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/log/out">abmelden</a>]
<?php }else{ ?>
	&raquo; Sali Vuz, Wanderer! [<a class="link" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/register">registrieren</a>|<a class="link" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/log">anmelden</a>]
<?php }?>
	</div>
	<h1>Kaiserreich Drachenstein</h1>
	<h2>Update</h2>
	<div id="topoptions"></div>
	<div id="content">
	<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['notice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
	<p class="notice notice_<?php echo $_smarty_tpl->tpl_vars['i']->value['type'];?>
"><?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars['i']->value['notice']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</p>
	<?php } ?>
	

	</div>
	<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
</body>
</html><?php }} ?>