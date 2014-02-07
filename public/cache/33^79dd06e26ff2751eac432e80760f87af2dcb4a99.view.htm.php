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
  'unifunc' => 'content_52f3c30e30c93',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f3c30e30c93')) {function content_52f3c30e30c93($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Verfassungsnovelle 1.5</title>
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
		<h2>Verfassungsnovelle 1.5</h2>
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
		
<h3>Verfassung Drachensteins - Novelle 1.5<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createencyclopedia')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/delete/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php }?></h3>
<p>&lt;B&gt;Verfassung des Kaiserreichs Drachenstein&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Pr&auml;ambel&lt;/B&gt;<br />
&lt;p&gt;<br />
Entstehend aus den noblen und w&uuml;rdigen H&auml;usern Arldroy, Esturien, Malazien, Pelata, Pisar, Pretanz und Vincaster gibt sich das Kaiserreich Drachenstein folgende Verfassung, die den gro&szlig;en Zielen und Traditionen aller K&ouml;nigsh&auml;user gerecht wird. <br />
&lt;p&gt;<br />
&lt;B&gt;I. Grundstruktur&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 1 [Kaiser]&lt;/B&gt;&lt;br&gt;<br />
Der Kaiser steht an der hierarchischen Spitze des Kaiserreichs. Er steht &uuml;ber allen Personen, der Verfassung und den Edikten. Als Kaiser gilt der erste m&auml;nnliche Nachkomme des letzten Kaisers, wenn dieser abgedankt hat. Der Kaiser kann Edikte erlassen, welche als niedergeschriebenes Recht dienen und vom Reichsconcilium best&auml;tigt werden m&uuml;ssen. Diese Edikte stehen unter der Verfassung und &uuml;ber den Edikten der K&ouml;nige. Der Kaiser Drachensteins ist gleichzeitig der K&ouml;nig von Pretanz. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 2 [K&ouml;nige]&lt;/B&gt;&lt;br&gt;<br />
Hierarchisch unter dem Kaiser stehen die K&ouml;nige der Provinzen Arldroy, Esturien, Malazien, Pelata, Pisar, Pretanz und Vincaster. Sie sind Beamte des Kaiserreiches Drachenstein und k&ouml;nnen zu jeder Zeit vom Kaiser ernannt und entlassen werden. Falls eine Person nicht zum K&ouml;nig ernannt werden will, kann sie eine Ablehnung liefern. Die K&ouml;nige k&ouml;nnen f&uuml;r ihre Provinzen Edikte erlassen, welche als niedergeschriebenes Recht der jeweiligen Provinz dienen. Diese Edikte stehen unter der Verfassung und den Edikten des Kaiserreichs. Die K&ouml;nige sind Mitglieder des Reichsconciliums. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 3 [Reichsconcilium]&lt;/B&gt;&lt;br&gt;<br />
Das Reichsconcilium besteht aus den K&ouml;nigen der Provinzen Arldroy, Esturien, Malazien, Pelata, Pisar und Vincaster und allen B&uuml;rgern, die gewillt sind, Mitglied des Reichsconciliums zu sein. Alle K&ouml;nige im Reichsconcilium geh&ouml;ren der K&ouml;nigskammer an und alle B&uuml;rger im Reichsconcilium geh&ouml;ren der Volkskammer an. Innerhalb einer Kammer z&auml;hlen alle Stimmen gleich. Jede Abstimmung wird in zwei Teilabstimmungen zerlegt, eine f&uuml;r jede Kammer. Im Endergebnis z&auml;hlt das Ergebnis jeder Kammer gleich. Sollte es im Endergebnis Stimme gegen Stimme stehen, so ist die Stimme des K&ouml;nigs von Pretanz als Kaiser von Drachenstein ausschlaggebend. Sollte es weniger B&uuml;rger in der Volkskammer als K&ouml;nige in der Reichskammer geben, so hat jeder K&ouml;nig noch einen Sitz in der Volkskammer. Das Reichsconcilium hat eine Hausordnung zu beschlie&szlig;en, welche die genauen Abl&auml;ufe im Reichsconcilium regelt. Alle Edikte des Kaisers von Drachenstein m&uuml;ssen durch das Reichsconcilium zu zwei Dritteln abgelehnt werden, um ihre G&uuml;ltigkeit zu verlieren. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 4 [R&uuml;ckzug des Kaisers]&lt;/B&gt;&lt;br&gt;<br />
Der Kaiser hat jederzeit die M&ouml;glichkeit, sich offiziell aus dem Reichsconcilium zur&uuml;ckzuziehen. Er ist dann dort nur noch als den anderen Mitgliedern der K&ouml;nigskammer gleichwertiger K&ouml;nig von Pretanz vertreten. Edikte k&ouml;nnen in diesem Falle von allen Mitgliedern des Reichsconciliums vorgeschlagen werden. Diese m&uuml;ssen dann drei Tage zur Diskussion stehen und anschlie&szlig;end vom Reichskanzler zur Abstimmung gestellt werden, wo nunmehr eine Zweidrittelmehrheit erforderlich ist, um ein Edikt g&uuml;ltig zu erkl&auml;ren. Das Edikt muss weiterhin vom Kaiser unterschrieben werden, um vollends g&uuml;ltig zu werden, dieser kann seine Unterschrift verweigern, wenn er im Edikt einen Versto&szlig; gegen den Willen der Verfassung oder gegen ein anderes Edikt sieht. Der Kaiser kann jederzeit wieder ins Reichsconcilium vorsto&szlig;en, womit diese Sonderregelungen au&szlig;er Kraft gesetzt werden und der normale Zustand wiederhergestellt wird. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 5 [Reichskanzler]&lt;/B&gt;&lt;br&gt;<br />
Der Reichskanzler ist Vorsitzender des Reichsconciliums. Er wird von allen Mitgliedern des Reichsconciliums gew&auml;hlt. Der Reichskanzler leitet die Conciliumssitzungen und repr&auml;sentiert das Kaiserreich. <br />
&lt;p&gt;<br />
&lt;B&gt;II. Staat&lt;/B&gt; <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 6 [Staatsgebiet]&lt;/B&gt;&lt;br&gt;<br />
Als Staatsgebiet gelten die Provinzen Arldroy, Esturien, Malazien, Pelata, Pisar, Pretanz und Vincaster sowie als Hoheitsgew&auml;sser alle Wassermassen, die weniger als 50 Kilometer von der K&uuml;ste des drakischen Festlands entfernt sind. So eingeschlossene Wassermassen z&auml;hlen ebenfalls zum Hoheitsgebiet. Dieses Gebiet wird als Kaiserreich Drachenstein bezeichnet und ist Geltungsbereich des drakischen Rechts. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 7 [Staatssymbole] &lt;/B&gt;&lt;br&gt;<br />
Als Staatssymbole gelten die Flagge, das Wappen, das Kaiserwappen und das kaiserliche Siegel. Das Kaiserwappen und das kaiserliche Siegel d&uuml;rfen nur vom Kaiserhaus selbst benutzt werden. Das Aussehen der Staatssymbole wird durch ein Edikt festgelegt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 8 [Nationalhymne] &lt;/B&gt;&lt;br&gt;<br />
Die Nationalhymne wird haupts&auml;chlich zu offiziellen Anl&auml;ssen gespielt und dient der Repr&auml;sentation des Staates. Die Melodie und der Text der Nationalhymne wird durch ein Edikt festgelegt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 9 [Amtssprache] &lt;/B&gt;&lt;br&gt;<br />
Als Amtssprache gilt die Drakische Sprache. Alle offiziellen Schriften werden in diesen Sprachen verfasst. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 10 [Hauptstadt und Regierungssitz] &lt;/B&gt;&lt;br&gt;<br />
Der Regierungssitz und die Hauptstadt des Kaiserreichs Drachenstein ist die Stadt Pretannica in der Provinz Pretanz. <br />
&lt;p&gt;<br />
&lt;B&gt;III. Innenpolitik&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 11 [Staatsb&uuml;rgerschaft] &lt;/B&gt;&lt;br&gt;<br />
Jeder hat das Recht, die drakische Staatsb&uuml;rgerschaft zu beantragen. Der Verlust der Staatsangeh&ouml;rigkeit darf nur aufgrund eines Gesetzes oder eines richterlichen Beschlusses eintreten, oder sobald ein drakischer Staatsb&uuml;rger dies w&uuml;nscht. Politisch Verfolgte genie&szlig;en in Drachenstein Asylrecht. Kein drakischer Staatsb&uuml;rger darf an das Ausland ausgeliefert werden. Die Staatsb&uuml;rgerschaft kann durch (a) Einb&uuml;rgerung oder (b) Vollendung der Geburt erlangt werden. Dies ist dem B&uuml;rgeramt zu melden. Im Falle (a) muss die Einb&uuml;rgerung &uuml;ber das B&uuml;rgeramt erfolgen und von selbigem best&auml;tigt werden, um G&uuml;ltigkeit zu erlangen. Im Falle (b) muss die Geburt und damit die Einb&uuml;rgerung dem B&uuml;rgeramt gemeldet werden. Die Staatsb&uuml;rgerschaft kann nicht erzwungen werden und ist vollkommen freiwillig. Sie darf jederzeit beendet werden, von Seiten des Betroffenen sowie von Seiten des Staates, jedoch von Seiten des Staates nur mit Begr&uuml;ndung. In diesem Falle wird der Betroffene jedoch aufgefordert, (a) unverz&uuml;glich ein Visum anzufordern oder den Staat zu verlassen, falls er staatenlos ist oder (b) unverz&uuml;glich die entsprechende Botschaft aufzusuchen, falls er in einem anderen von Drachenstein akzeptiertem Staat eine g&uuml;ltige Staatsb&uuml;rgerschaft besitzt. Der Betroffene gilt ebenfalls als staatenlos, wenn er B&uuml;rger in einem Staat ist, den das Kaiserreich Drachenstein nicht akzeptiert. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 12 [Eintritt der Vollj&auml;hrigkeit]&lt;/B&gt;&lt;br&gt;<br />
Der Eintritt der Vollj&auml;hrigkeit wird durch das Edikt zur Wesensbestimmung n&auml;her geregelt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 13 [Wohnsitz]&lt;/B&gt;&lt;br&gt;<br />
Wer sich an einem Ort st&auml;ndig und dauerhaft niederl&auml;sst, begr&uuml;ndet an diesem Orte seinen Wohnsitz. Der Wohnsitz eines B&uuml;rgers kann gleichzeitig an mehreren Orten bestehen. Ein Wohnsitz wird aufgehoben, wenn die Wohnst&auml;tte mit dem Willen aufgehoben wird, diese aufzugeben. Ein Kind hat seinen Wohnsitz stets dort, wo die Eltern bzw. die Vollmacht seinen Wohnsitz hat. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 14 [Namensrecht]&lt;/B&gt;&lt;br&gt;<br />
Wird der Name einer Person von einer Anderen missbraucht, so hat die erste Person das Recht auf Klage wegen Namensmissbrauch. <br />
&lt;p&gt;<br />
&lt;B&gt;IV. Aussenpolitik&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 15 [Aussenpolitische Ziele des Kaiserreichs]&lt;/B&gt;&lt;br&gt; <br />
Aussenpolitische Ziele des Kaiserreichs sind lang anhaltender Friede und Abkommen, die Vorteile f&uuml;r das Kaiserreich bringen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 16 [Akzeptanz von Staaten]&lt;/B&gt;&lt;br&gt;<br />
Ein Staat ausser das Kaiserreich Drachenstein gilt als offiziell akzeptiert und f&uuml;r vorhanden gefunden, wenn offizieller Kontakt besteht. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 17 [Botschaften] &lt;/B&gt;&lt;br&gt;<br />
Eine Botschaft ist eine dauerhafte Niederlassung von Regierungsvertretern eines fremden Staates in der Hauptstadt Drachensteins. Das Botschaftsgel&auml;nde samt Botschaftsgeb&auml;ude wird dem fremden Staat geschenkt und geh&ouml;rt zum Staatsgebiet dieses Staates. Auf dem Botschaftsgel&auml;nde gilt das Recht des fremden Staates. <br />
&lt;p&gt;<br />
&lt;B&gt;V. Wirtschaftspolitik&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 18 [Steuern]&lt;/B&gt;&lt;br&gt;<br />
Steuern werden in einem eigenen Edikt festgelegt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 19 [Firmengr&uuml;ndung]&lt;/B&gt;&lt;br&gt;<br />
Zur Firmengr&uuml;ndung wird ein Firmenvorstand und eine Lizenz ben&ouml;tigt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 20 [Lizenzerwerb]&lt;/B&gt;&lt;br&gt;<br />
Die Lizenz kann vom Kaiserreich Drachenstein f&uuml;r ein Entgeld von f&uuml;nf Tuk erworben werden. Mit Begr&uuml;ndung und Einverst&auml;ndnis des Kaisers kann manchen Firmen die Lizenz nicht gew&auml;hrleistet werden. Die Lizenz kann ebenso entzogen werden. In diesem Falle wird dem Firmengr&uuml;nder das Entgeld von f&uuml;nf Tuk zur&uuml;ckerstattet. Schlie&szlig;t der Firmenvorstand die Firma selbstst&auml;ndig, so besteht kein Anrecht auf die Lizenzgeb&uuml;hren. <br />
&lt;p&gt;<br />
&lt;B&gt;VI. Justiz&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 21 [Klagerecht] &lt;/B&gt;&lt;br&gt;<br />
Jeder B&uuml;rger darf Anklage erheben, solange es in einer schriftlichen und &ouml;ffentlichen Deklaration entsprechend der jeweiligen Gerichtsordnung vor dem Gericht geschieht. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 22 [Fallbearbeitung] &lt;/B&gt;&lt;br&gt;<br />
Der Fall wird vom jeweils dazu zust&auml;ndigen Gerichtshof bearbeitet. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 23 [Fallweitergabe] &lt;/B&gt;&lt;br&gt;<br />
Ein Gerichtshof kann die Fallbearbeitung an den n&auml;chsth&ouml;heren Gerichtshof weitergeben. Au&szlig;erdem muss auf Wunsch eines der Prozessbeteiligten nach Urteilsverk&uuml;ndung der n&auml;chsth&ouml;here Gerichtshof den Fall wieder aufnehmen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 24 [Wahldefinition in Gerichtsbelangen]&lt;/B&gt;&lt;br&gt;<br />
F&uuml;r die Wahl eines Gerichtsvorsitzenden w&auml;hlen s&auml;mtliche Gerichtsmitglieder am Tag der Gerichtsgr&uuml;ndung und von dort an alle zwei Monate aus ihrer Mitte in einem drei Tage dauernden gleichen und &ouml;ffentlichen Wahlvorgang und aus allen Personen, die sich fr&uuml;hestens sieben, sp&auml;testens aber zwei Tage vor der Wahl zur selbigen zur Verf&uuml;gung gestellt haben, einen Gerichtsvorsitzenden, wobei ein Kandidat zur Wahl zum Gerichtsvorsitzenden im ersten Wahlgang, welcher vom vorigen Gerichtsvorsitzenden, in Abstinenz desselbigen aber von der dem vorigen Gerichtsvorsitzenden in perpetualer aufsteigender alphabetischer Reihenfolge folgende Person geleitet wird, eine Zweidrittelmehrheit haben muss, ansonsten aber in einer auf den ersten Wahlgang folgenden Stichwahl zwischen den zwei Kandidaten mit der h&ouml;chsten Stimmanzahl in der vorgehenden Wahl, welche wie der erste Wahlgang geleitet wird, eine relative Mehrheit haben muss. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 25 [Gerichtsordnung]&lt;/B&gt;&lt;br&gt;<br />
Jeder offizielle Gerichtshof hat das Recht, eine Gerichtsordnung zu verfassen und zu bearbeiten, wobei jeder dieser Schritte vor der Ver&ouml;ffentlichung vom Gericht in einer gleichen, geheimen und vom Gerichtsvorsitzenden geleiteten Abstimmung mit ben&ouml;tigter Zweidrittelmehrheit best&auml;tigt und danach vom jeweiligigen Urteilsverk&uuml;nder unterzeichnet werden muss. In dieser sind der Ablauf einer Gerichtsverhandlung und die Rechte des Kl&auml;gers sowie des Beklagten genauer erl&auml;utert. Genaueres regelt ein Edikt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 26 [Reichsgerichtshof]&lt;/B&gt;&lt;br&gt;<br />
Als oberste Ebene gilt der Reichsgerichtshof. Er ist zust&auml;ndig f&uuml;r das gesamte Kaiserreich. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 27 [Mitglieder des Reichsgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Reichsgerichtshof besteht aus den K&ouml;nigen der Provinzen des Kaiserreiches als Richter, wobei der Vorsitzende des Reichsgerichtshofes entsprechend Artikel 24 gew&auml;hlt wird. Als Urteilsverk&uuml;nder gilt der Kaiser. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 28 [Zust&auml;ndigkeitsbereiche des Reichsgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Reichsgerichtshof behandelt Hochverrat, Verfassungsbruch, F&auml;lle, in denen eine Vermischung der Simulation und der Wirklichkeit vorkam, von anderen Gerichtsh&ouml;fen nicht behandelte Prozesse und von einem Provinzgerichtshof weitergeleitete Klagen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 29 [Provinzgerichtshof]&lt;/B&gt;&lt;br&gt;<br />
Als zweitoberste Ebene gilt der Provinzgerichtshof. Er ist zust&auml;ndig f&uuml;r jeweils eine Provinz und wird sp&auml;testens 2 Wochen, nachdem die B&uuml;rgerzahl in einer Provinz &uuml;ber zwei reelle B&uuml;rger steigt, wird aber sp&auml;testens 2 Wochen, nachdem die B&uuml;rgerzahl in einer Provinz unter drei reelle B&uuml;rger f&auml;llt, aufgel&ouml;st. Jede Provinz hat unter den in diesem Artikel genannten Bedingungen Anspruch auf einen Provinzgerichtshof. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 30 [Mitglieder des Provinzgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Provinzgerichtshof besteht aus den Adeligen einer Provinz als Richter, wobei der Vorsitzende des Provinzgerichtshofes entsprechend Artikel 24 gew&auml;hlt wird. Als Urteilsverk&uuml;nder gilt der K&ouml;nig. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 31 [Zust&auml;ndigkeitsbereiche des Provinzgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Provinzgerichtshof behandelt provinzinterne Ediktesbr&uuml;che auf Verwaltungsebene und von einem Stadtgerichtshof weitergeleitete Klagen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 32 [Stadtgerichtshof]&lt;/B&gt;&lt;br&gt;<br />
Als unterste Ebene gilt der Stadtgerichtshof. Er ist zust&auml;ndig f&uuml;r jeweils eine Provinz und wird sp&auml;testens 2 Wochen, nachdem die B&uuml;rgerzahl in einer Stadt &uuml;ber zwei reelle B&uuml;rger steigt, wird aber sp&auml;testens 2 Wochen, nachdem die B&uuml;rgerzahl in einer Stadt unter drei reelle B&uuml;rger f&auml;llt, aufgel&ouml;st. Jede Stadt hat unter den in diesem Artikel genannten Bedingungen Anspruch auf einen Stadtgerichtshof. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 33 [Mitglieder des Stadtgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Stadtgerichtshof besteht aus maximal drei vom jeweiligen Statthalter ernannten Personen und dem Statthalter als Richter, wobei der Vorsitzende des Stadtgerichtshofes entsprechend Artikel 24 gew&auml;hlt wird. Als Urteilsverk&uuml;nder gilt der Statthalter. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 34 [Zust&auml;ndigkeitsbereiche des Stadtgerichtshofes]&lt;/B&gt;&lt;br&gt;<br />
Der Stadtgerichtshof behandelt provinzinterne Ediktesbr&uuml;che auf ziviler Ebene sowie wirtschaftliche Ediktesbr&uuml;che. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 35 [Urteilsma&szlig;]&lt;/B&gt;&lt;br&gt;<br />
Urteile werden von den zust&auml;ndigen Richtern anhand von Vergleichsf&auml;llen, dem geltenden Recht und nach eigenem Ermessen gef&auml;llt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 36 [Urteilsanfechtung]&lt;/B&gt;&lt;br&gt;<br />
Urteile d&uuml;rfen gem&auml;&szlig; Artikel 23 angefochten werden, wird jedoch ein Urteil des Reichsgerichtshofes gem&auml;&szlig; Artikel 23 angefochten, so muss der Kaiser das Urteil &uuml;berpr&uuml;fen. Gilt es seiner Ansicht nach als falsch, so muss er dies dem Reichsgerichtshof mit einer Begr&uuml;ndung mitteilen, welcher daraufhin erneut den Prozess zu diesem Fall unter Zuhilfename &auml;lterer Prozesse zu diesem Fall er&ouml;ffnet und ein anderes Urteil f&auml;llt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 37 [Urteilsvollstreckung]&lt;/B&gt;&lt;br&gt;<br />
Ein Urteil wird vom Staat im Namen des Kaisers vollstreckt. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 38 [Richterschutz]&lt;/B&gt;&lt;br&gt;<br />
Ein Richter geniesst den besonderen Schutz des Staates, um seine Urteile nach freiem Gewissen und nicht nach physischen und psychischen Einwirkungen Dritter zu f&auml;llen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 39 [Prozessvertretung]&lt;/B&gt;&lt;br&gt;<br />
Eine Person hat in einem Prozess das Recht, sich durch einen Anwalt vertreten zu lassen, welcher eine frei w&auml;hlbare, vollm&uuml;ndige Person im Sinne geltenden Rechts ist. Dadurch geht das Recht einer Person, sich im Prozess selbst m&uuml;ndig zu &auml;u&szlig;ern, nicht verloren. Vertritt ein Anwalt nicht die Sache seines Mandanten nach einer Absprache mit demselbigen, so macht er sich strafbar. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 40 [Staatsvertretung]&lt;/B&gt;&lt;br&gt;<br />
Der Staat hat das Recht, sich in einem Prozess durch einen Staatsanwalt vertreten zu lassen, welcher im Prozess die Sache des Staates vertreten muss. F&uuml;r diesen Fall wird der Staat als Person und der Staatsanwalt als Anwalt deklariert, womit Artikel 39 g&uuml;ltig wird. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 41 [Prozessarchivierung]&lt;/B&gt;&lt;br&gt;<br />
Jeder Prozess muss archiviert und &ouml;ffentlich zug&auml;nglich gemacht werden. <br />
&lt;p&gt;<br />
&lt;B&gt;VII. Grundrechte&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 42 [W&uuml;rde des Wesens]&lt;/B&gt;&lt;br&gt;<br />
Die W&uuml;rde eines Wesens ist unantastbar. Als Wesen werden intelligente Organismen oder Magiegesch&ouml;pfe bezeichnet, die von dem staatlichen Institut f&uuml;r Wesenbestimmungen annerkannt werden. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 43 [Recht auf Leben]&lt;/B&gt;&lt;br&gt; <br />
Jedes Wesen hat das Recht auf Leben und k&ouml;rperliche Unversehrtheit. In diese Rechte darf nur auf Grund eines Gesetzes oder auf Kaiserliche Bestimmung eingegriffen werden. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 44 [Recht auf Freiheit]&lt;/B&gt;&lt;br&gt; <br />
Die Freiheit der Person ist unverletzlich. In diese Rechte darf nur auf Grund eines Gesetzes oder auf Kaiserliche Bestimmung eingegriffen werden. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 45 [Recht auf Handlungsfreiheit] &lt;/B&gt;&lt;br&gt;<br />
Die Handlungsfreiheit der Person ist unverletzlich. In diese Rechte darf nur auf Grund eines Gesetzes oder auf Kaiserliche Bestimmung eingegriffen werden. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 46 [Gleichheit] &lt;/B&gt;&lt;br&gt;<br />
Alle vom Staatlichen Institut f&uuml;r Wesensbestimmung als intelligente Wesen befundenen Wesen sind vor dem Gesetz gleich.<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 47 [Versammlungsfreiheit] &lt;/B&gt;&lt;br&gt;<br />
Alle Wesen haben das Recht, sich friedlich und ohne Waffen frei zu versammeln. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 48 [Eigentumsrecht] &lt;/B&gt;&lt;br&gt;<br />
Alle Wesen haben Recht auf Eigentum. <br />
&lt;p&gt;<br />
&lt;B&gt;VIII. Verfassungs&auml;nderung&lt;/B&gt;<br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 49 [Recht auf Verfassungs&auml;nderung] &lt;/B&gt;&lt;br&gt;<br />
Eine Verfassungs&auml;nderung kann nur durch den Kaiser mit dessen Einverst&auml;ndniserkl&auml;rung erfolgen. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 50 [Staatsform&auml;nderung] &lt;/B&gt;&lt;br&gt;<br />
Die monarchische Staatsform kann nicht Grund zur Verfassungs&auml;nderung sein. <br />
&lt;p&gt;<br />
&lt;B&gt;IX. Schlussbestimmungen&lt;/B&gt; <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 51 [Inkrafttreten] &lt;/B&gt;&lt;br&gt;<br />
Diese Verfassung tritt im Namen des Kaisers am 22. 02. 24024 nach drakischer Zeitrechnung in Kraft. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 52 [Versionsangabe] &lt;/B&gt;&lt;br&gt;<br />
Dies ist die Version 1.5. <br />
&lt;p&gt;<br />
&lt;B&gt;Artikel 53 [G&uuml;ltigkeitserkl&auml;rung] &lt;/B&gt;&lt;br&gt;<br />
Diese Verfassung wird mit der nachfolgenden Unterschrift und des Siegels des Kaisers g&uuml;ltig. <br />
&lt;p&gt;<br />
&lt;img src=&raquo;http://www.drakestrin.de/signaturen/siegelveuxin.gif&laquo;&gt; <br />
&lt;p&gt;<br />
Veuxin II. von Drachenstein&lt;br&gt;<br />
Kaetyr ent Drakestrin</p>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
&raquo; <a href="http://drakestrin.de/encyclopedia">Kompendium</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/3">Verwaltung</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/11">Verfassung</a> &rarr; Verfassungsnovelle 1.5</p>
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