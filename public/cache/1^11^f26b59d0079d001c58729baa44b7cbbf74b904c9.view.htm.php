<?php /*%%SmartyHeaderCode:84594107751a1c5000865c9-96652221%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f26b59d0079d001c58729baa44b7cbbf74b904c9' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/group/view.htm',
      1 => 1366133259,
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
    'cb084b72b160eb587448daeadd65b1c2d58d66db' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/bit_user.htm',
      1 => 1369399532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '84594107751a1c5000865c9-96652221',
  'cache_lifetime' => 86400,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_52f1e7621e79c',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f1e7621e79c')) {function content_52f1e7621e79c($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Gruppe &raquo;Nutzer&laquo;</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="http://drakestrin.de/templates/standard/_css/group_view.css" />
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
		<h2>Gruppe &raquo;Nutzer&laquo;</h2>
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
		
<h3>Mitglieder</h3>
<div class="pagination"><a href="http://drakestrin.de/group/view/1">1</a> <a href="http://drakestrin.de/group/view/1/2">2</a> <a href="http://drakestrin.de/group/view/1/3">3</a> <a href="http://drakestrin.de/group/view/1/4">4</a> <a href="http://drakestrin.de/group/view/1/5">5</a> <a href="http://drakestrin.de/group/view/1/6">6</a> <a href="http://drakestrin.de/group/view/1/7">7</a> <a href="http://drakestrin.de/group/view/1/8">8</a> <a href="http://drakestrin.de/group/view/1/9">9</a> <a href="http://drakestrin.de/group/view/1/10">10</a> <em>11</em> <a href="http://drakestrin.de/group/view/1/12">12</a> <a href="http://drakestrin.de/group/view/1/13">13</a> <a href="http://drakestrin.de/group/view/1/14">14</a></div>
<ol class="users">
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/223.jpg" />
	<a href="http://drakestrin.de/user/view/223">Graf von D&uuml;sterstein</a> 
	<p class="small">9 Beitr&auml;ge seit 27.03.2009, 05:32</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/224.jpg" />
	<a href="http://drakestrin.de/user/view/224">Friedbert Karlsson</a> 
	<p class="small">116 Beitr&auml;ge seit 30.03.2009, 22:44</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/225">Theonest Gentilis</a> 
	<p class="small">0 Beitr&auml;ge seit 06.04.2009, 08:39</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/226">Arakthe</a> 
	<p class="small">0 Beitr&auml;ge seit 21.04.2009, 15:17</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/227">Na&iuml;ra</a> 
	<p class="small">0 Beitr&auml;ge seit 23.04.2009, 19:41</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/228.jpg" />
	<a href="http://drakestrin.de/user/view/228">Periston d&#039;Angilo</a> 
	<p class="small">417 Beitr&auml;ge seit 28.04.2009, 00:12</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/229.jpg" />
	<a href="http://drakestrin.de/user/view/229">Caphalor</a> 
	<p class="small">65 Beitr&auml;ge seit 12.05.2009, 20:21</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/231.jpg" />
	<a href="http://drakestrin.de/user/view/231">Eiren Ziip</a> 
	<p class="small">1 Beitr&auml;ge seit 05.07.2009, 00:00</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/232">Peter Hukler</a> 
	<p class="small">0 Beitr&auml;ge seit 10.11.2009, 02:10</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/233">Alkarius von Alcazul</a> 
	<p class="small">4 Beitr&auml;ge seit 11.11.2009, 23:21</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/234.jpg" />
	<a href="http://drakestrin.de/user/view/234">Rumata von Esturien</a> 
	<p class="small">3 Beitr&auml;ge seit 05.12.2009, 02:49</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/235.jpg" />
	<a href="http://drakestrin.de/user/view/235">Marcus Flavius Celtillus</a> 
	<p class="small">9 Beitr&auml;ge seit 16.12.2009, 15:09</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/236.jpg" />
	<a href="http://drakestrin.de/user/view/236">Tadeus</a> 
	<p class="small">6 Beitr&auml;ge seit 18.12.2009, 23:06</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/237.jpg" />
	<a href="http://drakestrin.de/user/view/237">Nortius Erudio Xeledon</a> 
	<p class="small">29 Beitr&auml;ge seit 23.12.2009, 14:44</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/238.jpg" />
	<a href="http://drakestrin.de/user/view/238">Pericles Agrippa</a> 
	<p class="small">40 Beitr&auml;ge seit 23.12.2009, 23:55</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/239">Talshar</a> 
	<p class="small">0 Beitr&auml;ge seit 14.01.2010, 11:11</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/240.jpg" />
	<a href="http://drakestrin.de/user/view/240">Eldor Falathrim</a> 
	<p class="small">2 Beitr&auml;ge seit 17.01.2010, 22:07</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/241.jpg" />
	<a href="http://drakestrin.de/user/view/241">Medin</a> 
	<p class="small">7 Beitr&auml;ge seit 17.02.2010, 22:54</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/242">CoillUnatMoli</a> 
	<p class="small">0 Beitr&auml;ge seit 22.02.2010, 22:00</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/243.jpg" />
	<a href="http://drakestrin.de/user/view/243">Gordon Shumway</a> 
	<p class="small">8 Beitr&auml;ge seit 15.03.2010, 08:37</p>
</li>
</ol>
<div class="pagination"><a href="http://drakestrin.de/group/view/1">1</a> <a href="http://drakestrin.de/group/view/1/2">2</a> <a href="http://drakestrin.de/group/view/1/3">3</a> <a href="http://drakestrin.de/group/view/1/4">4</a> <a href="http://drakestrin.de/group/view/1/5">5</a> <a href="http://drakestrin.de/group/view/1/6">6</a> <a href="http://drakestrin.de/group/view/1/7">7</a> <a href="http://drakestrin.de/group/view/1/8">8</a> <a href="http://drakestrin.de/group/view/1/9">9</a> <a href="http://drakestrin.de/group/view/1/10">10</a> <em>11</em> <a href="http://drakestrin.de/group/view/1/12">12</a> <a href="http://drakestrin.de/group/view/1/13">13</a> <a href="http://drakestrin.de/group/view/1/14">14</a></div>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
 &raquo; <a href="http://drakestrin.de/group">Gruppen</a> &rarr; Gruppe &raquo;Nutzer&laquo;</p>
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