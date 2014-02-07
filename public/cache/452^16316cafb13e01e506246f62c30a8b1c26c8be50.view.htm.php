<?php /*%%SmartyHeaderCode:5096328225126cbafdadc51-63837238%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16316cafb13e01e506246f62c30a8b1c26c8be50' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/user/view.htm',
      1 => 1369441020,
      2 => 'file',
    ),
    'e757fb0429ab7f4d3c82a82accfeb5318b5625be' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_.htm',
      1 => 1369399530,
      2 => 'file',
    ),
    '5956e44a352249c972b82ce086b8e1e6339b5901' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/datetime.htm',
      1 => 1369399532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5096328225126cbafdadc51-63837238',
  'cache_lifetime' => 86400,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_52f45fd03b92a',
  'has_nocache_code' => true,
  'function' => 
  array (
    'datetime' => 
    array (
      'parameter' => 
      array (
        'objvalue' => NULL,
      ),
      'compiled' => '<?php if (!is_callable(\'smarty_modifier_date_format\')) include \'/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php\';
?>
<?php if ($_smarty_tpl->tpl_vars[\'value\']->value>=mktime(0,0,0)){?><strong>heute,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,$_smarty_tpl->tpl_vars[\'configuration\']->value->getConfiguration($_smarty_tpl->tpl_vars[\'objvalue\']->value,\'time\'));?>

<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value>=(mktime(0,0,0)-86400)){?><strong>gestern,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,$_smarty_tpl->tpl_vars[\'configuration\']->value->getConfiguration($_smarty_tpl->tpl_vars[\'objvalue\']->value,\'time\'));?>

<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value>=(mktime(0,0,0)-604800)){?><strong><?php if (smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==1){?>Montag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==2){?>Dienstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==3){?>Mittwoch<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==4){?>Donnerstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==5){?>Freitag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==6){?>Samstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,"%w")==0){?>Sonntag<?php }?>,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,$_smarty_tpl->tpl_vars[\'configuration\']->value->getConfiguration($_smarty_tpl->tpl_vars[\'objvalue\']->value,\'time\'));?>

<?php }else{ ?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,$_smarty_tpl->tpl_vars[\'configuration\']->value->getConfiguration($_smarty_tpl->tpl_vars[\'objvalue\']->value,\'date\'));?>
, <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value,$_smarty_tpl->tpl_vars[\'configuration\']->value->getConfiguration($_smarty_tpl->tpl_vars[\'objvalue\']->value,\'time\'));?>
<?php }?>',
      'nocache_hash' => '5096328225126cbafdadc51-63837238',
      'has_nocache_code' => false,
    ),
  ),
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f45fd03b92a')) {function content_52f45fd03b92a($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_replace')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: <br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="http://drakestrin.de/templates/standard/_css/user_id.css" />
<link rel="alternate" type="application/rss+xml" title="Kaiserreich Drachenstein RSS-Feed" href="rss.php" />
<link rel="icon" href="http://drakestrin.de/templates/standard/_img/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://drakestrin.de/templates/standard/_img/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="http://drakestrin.de/templates/standard/_js/jquery.min.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="ornament"></div>
		<h1><a href="http://drakestrin.de/index">Kaiserreich Drachenstein</a></h1>
		<ul>
			<li><a href="http://drakestrin.de/encyclopedia">Kompendium</a></li>
			<li><a href="http://drakestrin.de/board">Forum</a></li>
			<li><a href="http://drakestrin.de/search">Suche</a></li>
			<li><a href="http://drakestrin.de/user">Mitglieder</a></li> 
			<li><a href="http://drakestrin.de/territory">Lehen</a></li>
			<li><a href="http://drakestrin.de/calendar">Kalendarium</a></li> 
			<li><a href="http://drakestrin.de/dictionary">Diktionar</a></li>
		</ul>
		<div id="userbar"><div id="userbarcontent">
	<?php if (isset($_smarty_tpl->tpl_vars['user']->value)){?>
		<a id="notifypic" href="#"><img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/user_avatar.id/thumb/<?php if ($_smarty_tpl->tpl_vars['user']->value->avatar){?><?php echo $_smarty_tpl->tpl_vars['user']->value;?>
<?php }else{ ?>0<?php }?>.jpg" /></a>
		Sali Vuz, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['user']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value->name;?>
</a>! [<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/log/out">abmelden</a>]
	<?php }else{ ?>
		Sali Vuz, Wanderer! [<a class="link" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/register">registrieren</a>|<a class="link" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/log">anmelden</a>]
	<?php }?>
		</div></div>
		<h2><br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
</h2>
		<div id="content">
		<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['notice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
		<p class="notice notice_<?php echo $_smarty_tpl->tpl_vars['i']->value['type'];?>
"><?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars['i']->value['notice']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
</p>
		<?php } ?>
		
<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
<ol class="columns">
	<li><h4>Aktivit&auml;t</h4>
		<ol>
			<li><em>Mitglied seit:</em> <br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
, </li>
			<li><em>Letzter Besuch:</em> <?php Smarty_Internal_Function_Call_Handler::call ('datetime',$_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['obj']->value->lastvisit),'5096328225126cbafdadc51_63837238',true);?>
</li>
			<li><em>Beitr&auml;ge insgesamt:</em> <?php echo $_smarty_tpl->tpl_vars['obj']->value->post__total;?>
</li>
			<li><em>Beitr&auml;ge pro Tag:</em> <?php echo round(($_smarty_tpl->tpl_vars['obj']->value->post__total/((time()-$_smarty_tpl->tpl_vars['obj']->value->regdate)/86400)),"2");?>
</li>
		</ol>
	</li>
	<li><h4>Gruppen</h4>
		<ol>
			<li><a href="http://drakestrin.de/group/view/1">Nutzer</a></li>
		</ol>
	</li>
	<li><h4>Informationen<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="editieren">editieren</a><?php }?></h4>
		<ol>
		<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
		<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
		<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
		<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
		<br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
		</ol>
	</li>
</ol>


		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
 &raquo; <a href="http://drakestrin.de/user">Mitglieder</a> &rarr; <br />
<b>Notice</b>:  Undefined offset: 0 in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>220</b><br />
<br />
<b>Warning</b>:  Invalid argument supplied for foreach() in <b>/var/www/l3s658/html/drakestrin/library/class/model.php</b> on line <b>221</b><br />
</p>
			<div id="topoptions"></div>
			<?php if ($_smarty_tpl->tpl_vars['online']->value->data){?>
			<p id="online">
				Derzeit online: <br />
				<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['online']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
				<span>
					<img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/user_avatar.id/thumb/<?php if ($_smarty_tpl->tpl_vars['value']->value->user->avatar){?><?php echo $_smarty_tpl->tpl_vars['value']->value->user;?>
<?php }else{ ?>0<?php }?>.jpg" /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['value']->value->user;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->user->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a><br />
					<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value->time,$_smarty_tpl->tpl_vars['config']->value->time);?>
:
		<?php if ($_smarty_tpl->tpl_vars['value']->value->controller=='index'){?>
			<?php if ($_smarty_tpl->tpl_vars['value']->value->action=='std'){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
">Index</a>
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='online'){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
">Wer ist online?</a>
			<?php }else{ ?>Index, Seite <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
</a>
			<?php }?>
		<?php }elseif($_smarty_tpl->tpl_vars['value']->value->controller=='board'){?>
			<?php if ($_smarty_tpl->tpl_vars['value']->value->action=='viewall'){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
">Foren&uuml;bersicht</a>
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='view'){?>Forum &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo;
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='permissions'){?>Rechte im Forum &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo;
			<?php }else{ ?>Forum, Seite <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
</a>
			<?php }?>
		<?php }elseif($_smarty_tpl->tpl_vars['value']->value->controller=='thread'){?>
			<?php if ($_smarty_tpl->tpl_vars['value']->value->action=='view'){?>Thema &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo;
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='create'){?>Thema im Forum &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board/view/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo; erstellen
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='edit'){?>Thema &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo; bearbeiten
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='delete'){?>Thema &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo; l&ouml;schen
			<?php }else{ ?>Thema, Seite <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
</a>
			<?php }?>
		<?php }elseif($_smarty_tpl->tpl_vars['value']->value->controller=='user'){?>
			<?php if ($_smarty_tpl->tpl_vars['value']->value->action=='viewall'){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
">Mitglieder&uuml;bersicht</a>
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='view'){?>Mitglied &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>&laquo;
			<?php }elseif($_smarty_tpl->tpl_vars['value']->value->action=='edit'){?>Mitglied &raquo;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->location;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->location->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a> &laquo; bearbeiten
			<?php }else{ ?>Mitglieder, Seite <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
</a>
			<?php }?>
		<?php }else{ ?>Seite <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->controller;?>
/<?php echo $_smarty_tpl->tpl_vars['value']->value->action;?>
</a>
		<?php }?>
				</span>
				<?php } ?>
			</p>
			<?php }?>
		</div>
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="http://drakestrin.de/static/help">Hilfe</a>, <a href="http://drakestrin.de/static/terms">Nutzungsbedingungen</a>, <a href="http://drakestrin.de/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?>