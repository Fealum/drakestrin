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
  'unifunc' => 'content_52f3b3397613c',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f3b3397613c')) {function content_52f3b3397613c($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Drakischer Leitfaden</title>
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
		<h2>Drakischer Leitfaden</h2>
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
		
<h3>Der Drakische Leitfaden: Eine kurze F&uuml;hrung durch Drachenstein<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createencyclopedia')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/delete/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php }?></h3>
<p>Ihr seid nun also hier gelandet, habt aber keine Ahnung, wo Ihr seid oder was Ihr hier machen k&ouml;nnt? <br />
Dagegen gibt es jetzt diesen kurzen Leitfaden, der Euch erkl&auml;rt, wie Ihr helfen k&ouml;nnt, eine phantastische Welt mit Euren Gedanken auszugestalten! <br />
Doch bevor Ihr eure kurze F&uuml;hrung beginnt, erstmal eine &Uuml;bersicht, was man hier &uuml;berhaupt machen kann: <br />
<br />
<strong>I. Was ist Drachenstein? </strong><br />
1. Definition einer Micronation <br />
2. Die Situation Drachensteins <br />
3. Die Simulation <br />
<br />
<strong>II. Wie meldet man sich an? </strong><br />
1. Anmeldung im Forum <br />
2. Charaktererstellung <br />
3. Vorstellung im Forum <br />
<br />
<strong>III. Wie wird man aktiv? </strong><br />
1. Wahl des Wohnorts <br />
2. Beitritt bei einer Gilde <br />
3. Beitritt bei der Runischen Kirche <br />
<br />
Ihr seht, es sind nur drei mal drei Schritte, und schon k&ouml;nnt Ihr Euch einen B&uuml;rger Drachensteins nennen! Doch dazu sp&auml;ter mehr. Fangen wir mit einigen Erkl&auml;rungen an&hellip;<br />
<br />
<strong>I. Was ist Drachenstein? </strong><br />
<br />
1. Definition einer Micronation <br />
Eine Micronation ist die Simulation eines nicht existenten Staates. Dies erfolgt meistens &uuml;ber das Internet &ndash;&nbsp;so wie im Falle Drachensteins &ndash;&nbsp;und funktioniert wie ein Rollenspiel. Man schl&uuml;pft also in eine andere Rolle wie bei einem Theaterst&uuml;ck und lebt &ndash;&nbsp<img src="http://drakestrin.de/img/emoticon/37.gif" title="Frustriert sein">hne Drehbuch &ndash;&nbsp;in dieser fremden Welt vor sich hin. Wer schonmal DSA oder D&amp;D gespielt hat, sich also mit Pen&amp;Paper-Rollenspielen auskennt oder mit LARP, wird sich hier Bestens zurechtfinden. Alles was hier simuliert wird, hat nat&uuml;rlich keineswegs den Anspruch, wirklich so zu sein &ndash;&nbsp;gespielt wird in einer Fantasiewelt, wie etwa Mittelerde aus dem Herrn der Ringe, Phant&aacute;sien aus der Unendlichen Geschichte oder Narnia aus den Chroniken von Narnia. <br />
<br />
2. Die Situation Drachensteins <br />
Drachenstein simuliert ein fantasy-mittelalterliches Kaiserreich, in welchem es K&ouml;nige und F&uuml;rsten gibt, Marktweiber und Tuchh&auml;ndler, aber auch Orks und Oger, Vampire und Hobbits, Drachen und Magie. Dennoch ist die Welt Drachensteins eigenst&auml;ndig und hat ausgearbeitete Kulturen, die jedes Volk voneinander unterscheiden und Dinge wie Kleidung, Sprache und Kriegskunst umfassen. Man kennt in Drachenstein andere, &raquo;moderne&laquo; Dimensionen, ist jedoch nur wenig mit ihnen in Kontakt. Die Magie ist ein elementarer Bestandteil des Drakischen Lebens, jedoch nimmt sie eher die Stellung einer Religion als die Stellung eines Alltagsgebrauchsmittels ein. <br />
<br />
3. Die Simulation <br />
Es gibt einfache Regeln f&uuml;r die Simulation. Diese l&auml;uft ab wie ein Buch, welches von allen Beteiligten geschrieben wird: Es gibt Handlungen und w&ouml;rtliche Rede, die zu einem Teil von jedem Mitspieler hintereinander beigetragen werden k&ouml;nnen. Dennoch gibt es einige Regeln einzuhalten: So gilt ein geschriebener Fakt, der nicht unrealistisch ist, als stattgefunden und kann nicht ignoriert oder durch Anderes ersetzt werden, auch gilt es als sehr unh&ouml;flich und unerw&uuml;nscht, selbst die Handlungen der Mitspielercharaktere zu bestimmen, etwa zu sagen, dass ein Duellgegner stirbt oder schwer verwundet wird &ndash;&nbsp;dies ist im Rahmen des Realismus dem Spieler des jeweiligen Charakters &uuml;berlassen. <br />
<br />
Wenn Ihr jetzt noch am Lesen seid, fallen die W&uuml;rfel nicht schlecht, dass Ihr Interesse gewonnen habt &ndash;&nbsp;Ihr wollt also B&uuml;rger Drachensteins werden? <br />
<br />
<strong>II. Wie meldet man sich an?</strong><br />
<br />
<br />
1. Anmeldung im Forum<br />
Um sich bei Drachenstein anmelden zu k&ouml;nnen muss man zuerst auf &raquo;Registrieren&laquo; klicken. Daraufhin wird ein neues Fenster ge&ouml;ffnet. Hier klickt man einfach auf &raquo;Einverstanden&laquo;, nachdem man sich die Nutzungsbedingungen des Forums durchgelesen hat, und schon geht es weiter zum n&auml;chsten Fenster. <br />
In diesem neuen Fenster wird nach Eurem zuk&uuml;nftigen Nutzernamen gefragt. Diesen &uuml;berlegt Euch gut, da Ihr mit diesem Namen bekannt werdet. Dieser Name soll auf Euren Charakter passen und soll keine Beleidigung darstellen. Sonderzeichen sind beim Nutzernamen, abgesehen von Apostrophen oder Akzenten, nicht erlaubt. <br />
W&auml;hlt auch Euer Passwort mit Bedacht. Es sollte nicht Euer Nutzername sein und auch nicht Euer Geburtstag. Am Besten w&auml;re eine wahllose Buchstaben- und Zahlenkombination wie zum Beispiel &raquo;f7sdff19o&laquo;. <br />
Solche Passw&ouml;rter sind nicht so leicht zu knacken, aber leider auch nicht so leicht zu merken. <br />
 <br />
2. Charaktererstellung<br />
Die Charaktererstellung spielt in Drachenstein eine wichtige Rolle.  Man kann nicht einfach von einem Tag auf den anderen seinen Charakter &auml;ndern.  <br />
Was ist mit Charakter gemeint? Damit ist gemeint, wie sich Eure &raquo;Figur&laquo; in Zukunft verh&auml;lt. Ist sie sch&uuml;chtern, draufg&auml;ngerisch, abenteuerlustig, friedlich? Ihr habt die freie Wahl. Auch dies solltet Ihr nicht au&szlig;er Acht lassen, da man meist bestimmte Reaktionen aufgrund Eures Charakters erwartet. <br />
<br />
3. Vorstellung im Forum<br />
Nun kommen wir zur Vorstellung im Forum. Wenn Ihr nun Euren Nutzernamen und Charakter zufriedenstellend erstellt habt, seid Ihr aber noch kein B&uuml;rger Drachensteins. Dies werdet Ihr, indem Ihr im Unterforum &raquo;Marktschreier&laquo; ein neues Thema aufmacht mit dem Titel &raquo;Antrag auf Einb&uuml;rgerung&laquo;, und in diesem etwas &uuml;ber Euren Charakter und Seine Ziele in Drachenstein schreibt. So stellt Ihr Euren Charakter kurz vor. Schon nach Kurzem werdet Ihr stolzes Mitglied Drachensteins sein! <br />
<br />
<i>Weiteres folgt&hellip;</i><br />
<br />
<strong>Wen solltet Ihr in Drachenstein kennen? Dies sehr ihr bei der Auflistung der <a href="http://drakestrin.de/encyclopedia/view/39">wichtigen Personen</a>!</strong></p>
<h3>Unterseiten</h3>
<ol>
	<li><a href="http://drakestrin.de/encyclopedia/view/39">Wichtige Personen</a>
		</li>
</ol>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
&raquo; <a href="http://drakestrin.de/encyclopedia">Kompendium</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/1">Portal</a> &rarr; Drakischer Leitfaden</p>
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