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
  'unifunc' => 'content_52ed64885deed',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52ed64885deed')) {function content_52ed64885deed($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
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
<div class="pagination"><a href="http://drakestrin.de/group/view/1">1</a> <a href="http://drakestrin.de/group/view/1/2">2</a> <a href="http://drakestrin.de/group/view/1/3">3</a> <a href="http://drakestrin.de/group/view/1/4">4</a> <a href="http://drakestrin.de/group/view/1/5">5</a> <a href="http://drakestrin.de/group/view/1/6">6</a> <a href="http://drakestrin.de/group/view/1/7">7</a> <em>8</em> <a href="http://drakestrin.de/group/view/1/9">9</a> <a href="http://drakestrin.de/group/view/1/10">10</a> <a href="http://drakestrin.de/group/view/1/11">11</a> <a href="http://drakestrin.de/group/view/1/12">12</a> <a href="http://drakestrin.de/group/view/1/13">13</a> <a href="http://drakestrin.de/group/view/1/14">14</a></div>
<ol class="users">
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/161.jpg" />
	<a href="http://drakestrin.de/user/view/161">Bill T&uuml;r</a> 
	<p class="small">19 Beitr&auml;ge seit 10.12.2006, 21:55</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/162.jpg" />
	<a href="http://drakestrin.de/user/view/162">Iltera Dralant</a> 
	<p class="small">88 Beitr&auml;ge seit 17.12.2006, 13:52</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/163.jpg" />
	<a href="http://drakestrin.de/user/view/163">Hannibal</a> 
	<p class="small">34 Beitr&auml;ge seit 27.12.2006, 18:31</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/164">St Khimahra I</a> 
	<p class="small">1 Beitr&auml;ge seit 29.12.2006, 10:43</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/165.jpg" />
	<a href="http://drakestrin.de/user/view/165">Faith</a> 
	<p class="small">31 Beitr&auml;ge seit 01.01.2007, 22:57</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/166.jpg" />
	<a href="http://drakestrin.de/user/view/166">Ein kleines H&auml;schen</a> 
	<p class="small">92 Beitr&auml;ge seit 07.01.2007, 12:59</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/167">Gustav van Aylord</a> 
	<p class="small">2 Beitr&auml;ge seit 30.01.2007, 21:40</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/168.jpg" />
	<a href="http://drakestrin.de/user/view/168">Jeod</a> 
	<p class="small">193 Beitr&auml;ge seit 13.02.2007, 22:54</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/169">Sir Isaac Newton</a> 
	<p class="small">0 Beitr&auml;ge seit 14.02.2007, 21:46</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/170.jpg" />
	<a href="http://drakestrin.de/user/view/170">Veumir I. Trent</a> 
	<p class="small">5 Beitr&auml;ge seit 19.02.2007, 15:50</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/171.jpg" />
	<a href="http://drakestrin.de/user/view/171">Antonia Schellenberg</a> 
	<p class="small">6 Beitr&auml;ge seit 19.02.2007, 22:06</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/172.jpg" />
	<a href="http://drakestrin.de/user/view/172">Melima</a> 
	<p class="small">190 Beitr&auml;ge seit 19.02.2007, 22:43</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/173.jpg" />
	<a href="http://drakestrin.de/user/view/173">Der Ober&Ouml;rf</a> 
	<p class="small">79 Beitr&auml;ge seit 04.03.2007, 21:55</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/174.jpg" />
	<a href="http://drakestrin.de/user/view/174">Oberster Hirte</a> 
	<p class="small">9 Beitr&auml;ge seit 07.03.2007, 23:48</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/175.jpg" />
	<a href="http://drakestrin.de/user/view/175">Urmel</a> 
	<p class="small">1 Beitr&auml;ge seit 08.03.2007, 20:31</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/176.jpg" />
	<a href="http://drakestrin.de/user/view/176">Maya Mausal</a> 
	<p class="small">46 Beitr&auml;ge seit 17.03.2007, 19:22</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/177.jpg" />
	<a href="http://drakestrin.de/user/view/177">Hagorn</a> 
	<p class="small">843 Beitr&auml;ge seit 18.03.2007, 12:58</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/178">Gaby Ingelstaad</a> 
	<p class="small">1 Beitr&auml;ge seit 18.03.2007, 13:04</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/179.jpg" />
	<a href="http://drakestrin.de/user/view/179">Lillofee</a> 
	<p class="small">2 Beitr&auml;ge seit 18.03.2007, 16:55</p>
</li>
<li>
	<img src="http://drakestrin.de/img/user_avatar.id/thumb/0.jpg" />
	<a href="http://drakestrin.de/user/view/180">K&ouml;nig Potty</a> 
	<p class="small">0 Beitr&auml;ge seit 19.03.2007, 09:41</p>
</li>
</ol>
<div class="pagination"><a href="http://drakestrin.de/group/view/1">1</a> <a href="http://drakestrin.de/group/view/1/2">2</a> <a href="http://drakestrin.de/group/view/1/3">3</a> <a href="http://drakestrin.de/group/view/1/4">4</a> <a href="http://drakestrin.de/group/view/1/5">5</a> <a href="http://drakestrin.de/group/view/1/6">6</a> <a href="http://drakestrin.de/group/view/1/7">7</a> <em>8</em> <a href="http://drakestrin.de/group/view/1/9">9</a> <a href="http://drakestrin.de/group/view/1/10">10</a> <a href="http://drakestrin.de/group/view/1/11">11</a> <a href="http://drakestrin.de/group/view/1/12">12</a> <a href="http://drakestrin.de/group/view/1/13">13</a> <a href="http://drakestrin.de/group/view/1/14">14</a></div>

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