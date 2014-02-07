<?php /*%%SmartyHeaderCode:21262246885126d2adedc996-39122634%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '79dd06e26ff2751eac432e80760f87af2dcb4a99' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/view.htm',
      1 => 1361493158,
      2 => 'file',
    ),
    'e757fb0429ab7f4d3c82a82accfeb5318b5625be' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_.htm',
      1 => 1369399530,
      2 => 'file',
    ),
    '88319fd988ec356ba55fe6bcb8756d4033a7cdb0' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm',
      1 => 1366133164,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21262246885126d2adedc996-39122634',
  'cache_lifetime' => 86400,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_52ed3b82eab73',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52ed3b82eab73')) {function content_52ed3b82eab73($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Version 12. November 2006</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="http://drakestrin.de/templates/standard/_css/encyclopedia.css" />
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
		<h2>Version 12. November 2006</h2>
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
		
<h3>Die Hausordnung des Reichsconciliums vom 12. November 2006<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createencyclopedia')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/delete/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php }?></h3>
<p>&lt;B&gt;Hausordnung des Reichsconciliums&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;I&gt;I. Strukturierung&lt;/I&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Das Reichsconcilium besteht aus der Volkskammer und der K&ouml;nigskammer. Erstere besteht aus allen B&uuml;rgern Drachensteins, zweitere aus den K&ouml;nigen der f&uuml;nf Provinzen Drachensteins sowie Esturiens. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Jedes Mitglied des Reichsconciliums geniesst Stimmrecht. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Die Anzahl der Stimmen jedes Mitgliedes ist in der Verfassung festgelegt. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">4)&lt;/sup&gt; Alle K&ouml;nige im Reichsconcilium geh&ouml;ren der K&ouml;nigskammer an und alle B&uuml;rger im Reichsconcilium geh&ouml;ren der Volkskammer an. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">5)&lt;/sup&gt; Die Angelegenheiten des Reichsconciliums regelt der Reichskanzler. <br />
&lt;p&gt;<br />
&lt;I&gt;II. Reichskanzler&lt;/I&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Der Reichskanzler wird wie in der Verfassung verlangt von allen Mitgliedern der K&ouml;nigskammer des Reichsconciliums unter Ber&uuml;cksichtigung der Stimmenanzahl eines jeden Mitgliedes gew&auml;hlt. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Der Reichskanzler muss ein Mitglied des Reichsconciliums sein. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Der Reichskanzler sorgt f&uuml;r Ordnung im Reichsconcilium und f&uuml;r die Einhaltung dieser Hausordnung. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">4)&lt;/sup&gt; Die Amtszeit des Reichskanzlers dauert bis zu dessen R&uuml;cktritt durch Bescheid, durch Tod oder durch Abwahl durch das Reichsconcilium. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">5)&lt;/sup&gt; Der Reichskanzler kann vom Reichsconcilium auf Vorschlag eines Mitgliedes des Reichsconciliums in einer offenen Wahl unter Ber&uuml;cksichtigung der Stimmenverh&auml;ltnisse von den Mitgliedern der K&ouml;nigskammer des Reichsconciliums abgew&auml;hlt werden mit den Wahlm&ouml;glichkeiten &raquo;ja&laquo; und &raquo;nein&laquo;, die Wahl dauert drei Tage, um den Reichskanzler abzuw&auml;hlen, muss eine 2/3-Mehrheit der &raquo;nein&laquo;-Stimmen erreicht werden. <br />
&lt;p&gt;<br />
&lt;I&gt;III. Aufgaben&lt;/I&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Das Reichsconcilium &uuml;berpr&uuml;ft die Edikte des Kaisers und verabschiedet diese, wenn das Reichsconcilium nicht einstimmig gegen das jeweilige Edikt stimmt. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Das Reichsconcilium zeigt dem Kaiser auf Wunsch dessen die Meinungen der einzelnen K&ouml;nige zu einem genannten Thema in einer Deklaration auf, welche vom Reichskanzler erstellt wird und wortw&ouml;rtlich die jeweiligen Ansichten der einzelnen Mitglieder des Reichsconciliums wiedergeben muss. <br />
&lt;p&gt;<br />
&lt;I&gt;IV. Abstimmungen &uuml;ber Edikte&lt;/I&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Nach Vorlage des Edikts durch den Kaiser tr&auml;gt der Reichskanzler dieses Edikt dem Reichsconcilium vor. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Nach dem Vortragen des Edikts durch den Reichskanzler stimmen die Mitglieder des Reichsconciliums in einer offenen Wahl unter Ber&uuml;cksichtigung der Stimmenverh&auml;ltnisse &uuml;ber das Edikt ab mit den Wahlm&ouml;glichkeiten &raquo;ja&laquo; und &raquo;nein&laquo; und einer Abstimmungsdauer von drei Tagen. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Sollte das Edikt nicht mit Zweidrittelmehrheit vom Reichsconcilium abgelehnt werden, so wird es vom Reichskanzler verifiziert und als geltend verabschiedet. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">4)&lt;/sup&gt; Sollte das Edikt einstimmig vom Reichsconcilium abgelehnt werden, so muss jedes Mitglied dem Reichskanzler die Gr&uuml;nde f&uuml;r seine Ablehnung des Edikts mitteilen, der Reichskanzler muss diese dann in einer f&ouml;rmlichen Ablehnungsdeklaration dem Kaiser weiterleiten. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">5)&lt;/sup&gt; Ist eine erforderliche Mehrheit w&auml;hrend einer Abstimmung schon vor Ablauf der drei Tage gegeben, so kann der Reichskanzler diese verzeitig beenden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">6)&lt;/sup&gt; Innerhalb einer Kammer z&auml;hlen alle Stimmen gleich. Jede Abstimmung wird in zwei Teilabstimmungen zerlegt, eine f&uuml;r jede Kammer. Im Endergebnis z&auml;hlt das Ergebnis jeder Kammer gleich. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">7)&lt;/sup&gt; Steht es im Endergebnis Stimme gegen Stimme, so ist die Stimme des K&ouml;nigs von Pretanz als Kaiser von Drachenstein ausschlaggebend <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen"><img src="http://drakestrin.de/img/emoticon/57.gif" title=":cool:">&lt;/sup&gt; Sollte es weniger B&uuml;rger in der Volkskammer als K&ouml;nige in der Reichskammer geben, so hat jeder K&ouml;nig noch einen Sitz in der Volkskammer. <br />
&lt;p&gt;<br />
&lt;I&gt;V. Ver&auml;nderung der Hausordnung&lt;/I&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Auf Anregung eines Mitglieds des Reichsconciliums kann &uuml;ber eine Ver&auml;nderung der Hausordnung in einer offenen Wahl unter Ber&uuml;cksichtigung der jeweiligen Stimmenverh&auml;ltnisse und einer Dauer von 3 Tagen entschieden werden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Es ist eine 2/3-Mehrheit f&uuml;r die &Auml;nderung der Hausordnung erforderlich, um diese geltend zu machen. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Ist eine erforderliche Mehrheit w&auml;hrend einer Abstimmung schon vor Ablauf der drei Tage gegeben, so kann der Reichskanzler diese vorzeitig beenden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">4)&lt;/sup&gt; &Uuml;ber ein Au&szlig;erkraftsetzen der Hausordnung muss wie &uuml;ber eine &Auml;nderung der Hausordnung abgestimmt werden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">5)&lt;/sup&gt; Wird die Hausordnung au&szlig;er Kraft gesetzt, so ist sofort der Entwicklungsprozess f&uuml;r eine neue Hausordnung zu beginnen, &uuml;ber welche wie bei einer &Auml;nderung der Hausordnung abgestimmt werden muss. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">6)&lt;/sup&gt; Sollte nach dem Au&szlig;erkraftsetzen der Hausordnung nach sieben Tagen noch keine neue Hausordnung zustande gekommen sein, wird die vorherige Hausordnung wieder g&uuml;ltig. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">7)&lt;/sup&gt; W&auml;hrend des Prozesses der Entwicklung einer neuen Hausordnung auf Grund des Au&szlig;erkraftsetzens der vorherigen Hausordnung gilt die vorherige Hausordnung bis zur legitimen G&uuml;ltigkeitserkl&auml;rung der neuen Hausordnung. </p>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
&raquo; <a href="http://drakestrin.de/encyclopedia">Kompendium</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/3">Verwaltung</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/13">Hausordnungen</a> &rarr; Version 12. November 2006</p>
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