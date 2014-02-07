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
  'unifunc' => 'content_52f3185f4e9ff',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f3185f4e9ff')) {function content_52f3185f4e9ff($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Edikt zum Begriff des B&uuml;rgers</title>
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
		<h2>Edikt zum Begriff des B&uuml;rgers</h2>
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
		
<h3>Edikt zum Begriff des B&uuml;rgers<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createencyclopedia')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/delete/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php }?></h3>
<p>&lt;b&gt;Edikt zum Begriff des B&uuml;rgers&lt;/b&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Zum Wohle des Kaiserreichs erl&auml;sst der Kaiser Veuxin II. von Drachenstein folgendes Edikt: &lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;I. Staatsb&uuml;rger&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 1 [Definition]&lt;/i&gt;<br />
&lt;br&gt;Staatsb&uuml;rger oder B&uuml;rger des Kaiserreichs Drachenstein ist, wer einer Art im Sinne des drakischen Rechts angeh&ouml;rt und einen B&uuml;rgerschaftsantrag gestellt hat, welcher erfolgreich angenommen wurde. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 2 [B&uuml;rgerschaftsantrag]&lt;/i&gt;<br />
&lt;br&gt;Wer Staatsb&uuml;rger werden m&ouml;chte, hat einen B&uuml;rgerschaftsantrag an das kaiserliche B&uuml;rgeramt Drachensteins zu stellen. Dieser ist ein formloses Schreiben mit der Bitte, als Staatsb&uuml;rger aufgenommen zu werden. Wird diesem Schreiben stattgegeben, so muss der Anw&auml;rter auf den Titel des Staatsb&uuml;rgers ein Formular des B&uuml;rgeramts korrekt ausf&uuml;llen, welches Grundfragen zur Person enth&auml;lt. Diese Angaben werden nun auf ihre Rechtsvertr&auml;glichkeit &uuml;berpr&uuml;ft. Bei erfolgreicher Pr&uuml;fung muss der Anw&auml;rter noch einen Eid auf den Kaiser und auf die Verfassung schw&ouml;ren und wird dann offiziell als Staatsb&uuml;rger in das B&uuml;rgerregister eingetragen. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 3 [Bedingungen zum Erlangen der B&uuml;rgerschaft]&lt;/i&gt;<br />
&lt;br&gt;Wer Staatsb&uuml;rger werden m&ouml;chte, <br />
&lt;br&gt;- muss schon einen Monat oder l&auml;nger ein Bewohner Drachensteins sein, <br />
&lt;br&gt;- muss dem Kaiser und seinem K&ouml;nig unterlegen sein, <br />
&lt;br&gt;- muss in den letzten zwei Monaten keine Straftaten nach drakischem Recht begangen haben und <br />
&lt;br&gt;- muss einer Art im Sinne des drakischen Rechts angeh&ouml;ren. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 4 [Erreichbarkeitspflicht]&lt;/i&gt;<br />
&lt;br&gt;Wer Staatsb&uuml;rger ist, muss jederzeit gesichert innerhalb von zwei Wochen erreichbar sein, sollte er sich nicht im Vornherein f&uuml;r seine Abwesenheit &uuml;ber einen gewissen Zeitraum entschuldigt haben oder aber im Nachhinein einen vom Reichsgericht f&uuml;r gerechtfertigt gehaltenen Grund auf Verlangen des Reichsgerichtes vorweisen k&ouml;nnen. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 5 [Rechte und Pflichten]&lt;/i&gt;<br />
&lt;br&gt;Ein Staatsb&uuml;rger genie&szlig;t alle im drakischen Recht verankerten Rechte und Pflichten, welche f&uuml;r Staatsb&uuml;rger oder f&uuml;r Bewohner gelten. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 6 [Verlust der Staatsb&uuml;rgerschaft]&lt;/i&gt;<br />
&lt;br&gt;Ein Staatsb&uuml;rger kann seine Staatsb&uuml;rgerschaft nur durch Beschluss des Reichsgerichtes oder durch Nichterf&uuml;llen der Definition in Artikel 1 verlieren. In ersterem Fall muss der Urteilsspruch f&uuml;r die entsprechende Person als Strafe einen Verlust der Staatsb&uuml;rgerschaft oder eine Verbannung beinhalten. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 7 [Niederlegen der Staatsb&uuml;rgerschaft]&lt;/i&gt;<br />
&lt;br&gt;Ein Staatsb&uuml;rger kann jederzeit seine Staatsb&uuml;rgerschaft niederlegen. Stirbt ein Staatsb&uuml;rger, so legt er seine Staatsb&uuml;rgerschaft ebenfalls nieder. <br />
&lt;p&gt;<br />
&lt;i&gt;II. Bewohner&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 8 [Definition]&lt;/i&gt;<br />
&lt;br&gt;Bewohner des Kaiserreichs Drachenstein ist, wer einer Art im Sinne des drakischen Rechts angeh&ouml;rt und in Drachenstein einen dauerhaften Wohnsitz ist. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 9 [Rechte und Pflichten]&lt;/i&gt;<br />
&lt;br&gt;Ein Bewohner genie&szlig;t alle im drakischen Recht verankerten Rechte und Pflichten, welche f&uuml;r Bewohner gelten. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 10 [Verlust des Status eines Bewohners]&lt;/i&gt;<br />
&lt;br&gt;Ein Bewohner ist kein Bewohner mehr, wenn er in Drachenstein keinen dauerhaften Wohnsitz mehr hat oder nicht mehr zu einer Art im Sinne des drakischen Rechts geh&ouml;rt. <br />
&lt;p&gt;<br />
&lt;i&gt;III. Besucher&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 11 [Definition]&lt;/i&gt;<br />
&lt;br&gt;Besucher des Kaisereichs Drachenstein ist, wer einer Art im Sinne des drakischen Rechts angeh&ouml;rt und keinen dauerhaften Wohnsitz in Drachenstein hat, sich aber in Drachenstein aufh&auml;lt. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 12 [Rechte und Pflichten]&lt;/i&gt;<br />
&lt;br&gt;Ein Besucher genie&szlig;t alle im drakischen Recht verankerten Rechte und Pflichten, welche f&uuml;r einen Angeh&ouml;rigen einer Art im Sinne des drakischen Rechts gelten. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 13 [Verlust des Status eines Besuchers]&lt;/i&gt;<br />
&lt;br&gt;Wer das Kaiserreich Drachenstein verl&auml;sst und ein Besucher ist, verliert diesen Status. <br />
&lt;p&gt;<br />
&lt;i&gt;IV. Tier&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 14 [Definition]&lt;/i&gt;<br />
&lt;br&gt;Tier ist, wer keiner Art im Sinne des drakischen Rechts angeh&ouml;rt. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 15 [Rechte und Pflichten]&lt;/i&gt;<br />
&lt;br&gt;Ein Tier genie&szlig;t alle im drakischen Recht verankerten Rechte und Pflichten, welche f&uuml;r ein Wesen gelten, welches kein Angeh&ouml;riger einer Art im Sinne des drakischen Rechts ist. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 16 [Verlust des Status eines Tieres]&lt;/i&gt;<br />
&lt;br&gt;Den Status eines Tieres verliert, wer Besucher, Bewohner oder Staatsb&uuml;rger des Kaiserreiches Drachenstein ist. <br />
&lt;p&gt;<br />
&lt;i&gt;V. B&uuml;rgeramt&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 17 [Aufgaben und Leitung]&lt;/i&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Zust&auml;ndig f&uuml;r <br />
&lt;br&gt;- die Aufnahme, <br />
&lt;br&gt;- die Registrierung, <br />
&lt;br&gt;- die Verwaltung der Daten und <br />
&lt;br&gt;- den B&uuml;rgerschaftsverlust<br />
&lt;br&gt;von Staatsb&uuml;rgern ist das B&uuml;rgeramt. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Der Vorgang dieser Aufgaben muss f&uuml;r jeden B&uuml;rger streng nach den in Artikel 18 festgelegten Richtlinien verlaufen. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Die Leitung des B&uuml;rgeramts wird vom Kaiser oder einer vom Kaiser dazu bem&auml;chtigten Person ernannt und entlassen. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 18 [Vorgang der Aufgaben]&lt;/i&gt;<br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">1)&lt;/sup&gt; Die Aufnahme eines B&uuml;rgers muss nach dem in Artikel 2 genannten Verfahren geschehen. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">2)&lt;/sup&gt; Das in Artikel 2 genannte Formular muss Fragen zur Person des Antragstellers enthalten und soll allgemeing&uuml;ltig sein, darf jedoch nach neuesten Anforderungen umgestaltet werden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">3)&lt;/sup&gt; Die Verwaltung der Daten eines Staatsb&uuml;rgers muss sicher und geordnet geschehen. Die Grundinformationen nach Artikel 19 m&uuml;ssen jedem Bewohner des Kaiserreiches Drachenstein zug&auml;nglich sein, die erweiterten Informationen nach Artikel 19 d&uuml;rfen nur Richtern zur Hilfe bei Justizf&auml;llen und Beamten auf schriftlichen Antrag des K&ouml;nigs der entsprechenden Person oder auf schriftlichen Antrag des Kaisers zug&auml;nglich gemacht werden. <br />
&lt;br&gt;&lt;sup&gt<img src="http://drakestrin.de/img/emoticon/38.gif" title="Weinen">4)&lt;/sup&gt; Bei B&uuml;rgerschaftsverlust muss dieses in der Akte des entsprechenden B&uuml;rgers mit Datum und Grund vermerkt werden, die Akte muss allerdings weiterhin erhalten bleiben. <br />
&lt;p&gt;<br />
&lt;i&gt;Artikel 19 [Zu verwaltende Informationen &uuml;ber B&uuml;rger]&lt;/i&gt;<br />
&lt;br&gt;Zu den Grundinformationen &uuml;ber einen B&uuml;rger geh&ouml;ren <br />
&lt;br&gt;- sein vollst&auml;ndiger Name, <br />
&lt;br&gt;- sein Geburtsdatum, <br />
&lt;br&gt;- sein Todesdatum, <br />
&lt;br&gt;- seine Adresse <br />
&lt;br&gt;- und seine Staatsangeh&ouml;rigkeiten. <br />
&lt;br&gt;Zu den Erweiterten Informationen &uuml;ber einen B&uuml;rger geh&ouml;ren <br />
&lt;br&gt;- seine Religionszugeh&ouml;rigkeit, <br />
&lt;br&gt;- sein durchschnittliches Einkommen, <br />
&lt;br&gt;- seine erlernten Berufe, <br />
&lt;br&gt;- seine momentane Arbeit, <br />
&lt;br&gt;- sein Lebenslauf, <br />
&lt;br&gt;- sein Stammbaum, <br />
&lt;br&gt;- sein Vorstrafenregister, <br />
&lt;br&gt;- seine Gerichtsakten, <br />
&lt;br&gt;- seine bisherigen Berufe, <br />
&lt;br&gt;- seine Gildenzugeh&ouml;rigkeit, <br />
&lt;br&gt;- seine Mitgliedschaften in Interessensgruppen, <br />
&lt;br&gt;- seine Grundst&uuml;cksinformationen, <br />
&lt;br&gt;- sein registrierpflichtiger Besitz, <br />
&lt;br&gt;- seine amtlichen Verpflichtungen, <br />
&lt;br&gt;- seine Gef&auml;ngnistage, <br />
&lt;br&gt;- das Datum seiner Einb&uuml;rgerung, <br />
&lt;br&gt;- eventuell das Datum der Ausb&uuml;rgerungen und der Wiedereinb&uuml;rgerungen <br />
&lt;br&gt;- und s&auml;mtliche Informationsaufnahmen von beh&ouml;rdlichen Stellen. <br />
&lt;p&gt;<br />
&lt;i&gt;So beschlossen von Kaiser Veuxin II. von Drachenstein am 7. Termophles 24026&lt;/i&gt;</p>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
&raquo; <a href="http://drakestrin.de/encyclopedia">Kompendium</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/3">Verwaltung</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/9">Edikte</a> &rarr; Edikt zum Begriff des B&uuml;rgers</p>
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