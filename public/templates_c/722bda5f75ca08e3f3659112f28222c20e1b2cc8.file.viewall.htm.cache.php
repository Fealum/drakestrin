<?php /* Smarty version Smarty-3.1.5, created on 2013-05-25 03:19:26
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/viewall.htm" */ ?>
<?php /*%%SmartyHeaderCode:9779403605126c6805dacb7-73383732%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '722bda5f75ca08e3f3659112f28222c20e1b2cc8' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/viewall.htm',
      1 => 1361493158,
      2 => 'file',
    ),
    'e757fb0429ab7f4d3c82a82accfeb5318b5625be' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_.htm',
      1 => 1369399530,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9779403605126c6805dacb7-73383732',
  'function' => 
  array (
    'encyclopedia' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '
<ol>
<?php  $_smarty_tpl->tpl_vars[\'page\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'page\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'data\']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'page\']->key => $_smarty_tpl->tpl_vars[\'page\']->value){
$_smarty_tpl->tpl_vars[\'page\']->_loop = true;
?>	<li><a href="<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/encyclopedia/view/<?php echo $_smarty_tpl->tpl_vars[\'page\']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'page\']->value->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
</a>
	<?php if ($_smarty_tpl->tpl_vars[\'page\']->value->encyclopedia__1){?><?php Smarty_Internal_Function_Call_Handler::call (\'encyclopedia\',$_smarty_tpl,array(\'data\'=>$_smarty_tpl->tpl_vars[\'page\']->value->encyclopedia__1),\'9779403605126c6805dacb7_73383732\',false);?>
<?php }?>
	</li>
<?php } ?>
</ol>',
      'nocache_hash' => '9779403605126c6805dacb7-73383732',
      'has_nocache_code' => false,
      'called_functions' => 
      array (
        0 => 'encyclopedia',
      ),
    ),
  ),
  'cache_lifetime' => 86400,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5126c6806f974',
  'variables' => 
  array (
    'config' => 0,
    'user' => 1,
    'notice' => 1,
    'i' => 1,
    'online' => 1,
    'value' => 1,
  ),
  'has_nocache_code' => true,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5126c6806f974')) {function content_5126c6806f974($_smarty_tpl) {?><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php $_smarty = $_smarty_tpl->smarty; if (!is_callable(\'smarty_modifier_date_format\')) include \'/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php\';
?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: Kompendium</title>
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
	<div id="wrapper">
		<div id="ornament"></div>
		<h1><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/index">Kaiserreich Drachenstein</a></h1>
		<ul>
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
		<div id="userbar"><div id="userbarcontent">
	<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if (isset($_smarty_tpl->tpl_vars[\'user\']->value)){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<a id="notifypic" href="#"><img src="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'user\']->value->avatar){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
0<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
.jpg" /></a>
		Sali Vuz, <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value->name;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>! [<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/log/out">abmelden</a>]
	<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		Sali Vuz, Wanderer! [<a class="link" href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/register">registrieren</a>|<a class="link" href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/log">anmelden</a>]
	<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		</div></div>
		<h2>Kompendium</h2>
		<div id="content">
		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php  $_smarty_tpl->tpl_vars[\'i\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'i\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'notice\']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'i\']->key => $_smarty_tpl->tpl_vars[\'i\']->value){
$_smarty_tpl->tpl_vars[\'i\']->_loop = true;
?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<p class="notice notice_<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'i\']->value[\'type\'];?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars[\'i\']->value[\'notice\']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</p>
		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php } ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		
<?php Smarty_Internal_Function_Call_Handler::call ('encyclopedia',$_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value->data),'9779403605126c6805dacb7_73383732',false);?>


		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a> &rarr; Kompendium</p>
			<div id="topoptions"></div>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'online\']->value->data){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			<p id="online">
				Derzeit online: <br />
				<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php  $_smarty_tpl->tpl_vars[\'value\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'value\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'online\']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'value\']->key => $_smarty_tpl->tpl_vars[\'value\']->value){
$_smarty_tpl->tpl_vars[\'value\']->_loop = true;
?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

				<span>
					<img src="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->user->avatar){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
0<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
.jpg" /><a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->user->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a><br />
					<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value->time,$_smarty_tpl->tpl_vars[\'config\']->value->time);?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
:
		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'index\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'std\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
">Index</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'online\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
">Wer ist online?</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Index, Seite <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'board\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
">Foren&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'permissions\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Rechte im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Forum, Seite <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'thread\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'create\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Thema im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/board/view/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo; erstellen
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'delete\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo; l&ouml;schen
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Thema, Seite <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'user\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
">Mitglieder&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a> &laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Mitglieder, Seite <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }else{ ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
Seite <a href="<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
"><?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
/<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>
</a>
		<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

				</span>
				<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php } ?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

			</p>
			<?php echo '/*%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/<?php }?>/*/%%SmartyNocache:9779403605126c6805dacb7-73383732%%*/';?>

		</div>
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?>