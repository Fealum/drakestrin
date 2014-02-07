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
  'unifunc' => 'content_52f3b142ef967',
  'has_nocache_code' => true,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52f3b142ef967')) {function content_52f3b142ef967($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Geschichte</title>
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
		<h2>Geschichte</h2>
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
		
<h3>Geschichte<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createencyclopedia')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'editencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php }?><?php if ((Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==2||(Permission::getPermission($_smarty_tpl->tpl_vars['obj']->value,'deleteencyclopedia')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->user->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/delete/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php }?></h3>
<p>&lt;b&gt;Gr&uuml;ndungsjahre&lt;/b&gt;&lt;br&gt;<br />
Martinus I. von Drachenstein, ein r&ouml;mischer Seefahrer und Entdecker, erlitt im Jahre 0 ndZ (4000vChr) Schiffbruch. Noch war ihm nicht bewusst, dass er auf der Insel Pretanz gelandet war, die vor ihm noch kein Mensch betreten hatte. Lediglich die restlichen V&ouml;lker Drachensteins, die Elben, Zwerge, Vampire, Drachen und Hobbits &ndash;&nbsp;um nur ein paar zu nennen &ndash;&nbsp;waren schon vorhanden, allerdings als unorganisierte Clans, die durch das Land zogen und sich selber bek&auml;mpften. Nach einer kurzen Phase des Friedens &ndash;&nbsp;die Schiffbr&uuml;chigen, die noch nicht ahnten, das sie nicht allein waren, hatten ein provisorisches Lager errichtet &ndash;&nbsp;traf Martinus I. auf einen schlafenden roten Drachen von gigantischen Ausma&szlig;en. Als er n&auml;her kam, wachte der Drache auf und die beiden f&uuml;hrten einen erbitterten Zweikampf. Nachdem Martinus I. ihn verwundet hatte (&uuml;ber die genaue K&ouml;rperstelle gibt es verschiedene Angaben), gab der Drache auf und stellte sich in den Dienst von Drachenstein. Martinus I. rief das K&ouml;nigreich des Drachen, das Drachenreich, aus und ernannte sich zum Herrscher. Das Herrschaftsgebiet betrug erst die Insel Pretanz, als Flagge diente der rote Drache namens Xer, abgebildet auf wei&szlig;em Grund. An diesem Tage lie&szlig; Martinus I. als Zeichen des neuen K&ouml;nigreichs ein Kreuz aufstellen mit der Inschrift: Ich steh&#039; f&uuml;r Fried&#039; f&uuml;r Freud&#039; und Ehr, / f&uuml;r wen&#039;ger nicht und nicht f&uuml;r mehr. / Gro&szlig; gelobet sei der Tag, / seit dem ich in den Himmel rag&#039;. / Gro&szlig;e Leiden, Ehr&#039; und Gl&uuml;ck / sah ich alles St&uuml;ck f&uuml;r St&uuml;ck. / Drum denk&#039; daran und neige dich, / vor den&#039; welch leben ewiglich. (Freie &Uuml;bersetzung nach den alten Schriften). Mit dem Zusatz &raquo;Ein Drache sei ich, Gr&uuml;n auf Rot, f&uuml;r unser&#039;n Stolz, der niemals tot. &laquo; ist diese Inschrift heute Teil der Drachensteiner Nationalhymne. Heute ist dieses Kreuz verschollen, sein Aussehen ist nur noch anhand von alten Zeichnungen zu rekonstruieren gewesen. Angeblich steht es in den uralten Gew&ouml;lben des Schlosses Pretannica. F&uuml;nf drakische Jahre sp&auml;ter (3999vChr) heiratete Martinus I. von Drachenstein in einer festlichen Zeremonie Fiona I. von Drachenstein. Der Bau an einer Festung an der Stelle des heutigen Schlosses Pretannica war schon weit fortgeschritten, erste Gew&ouml;lbeabschnitte entstanden. Sp&auml;ter einmal besteht das Gew&ouml;lbe aus einem Flickenteppich verschiedener Baustile, da jeder Kaiser ein St&uuml;ck anf&uuml;gen wollte. Das Gew&ouml;lbe ist heutzutage riesig und untertunnelt fast ganz Pretannica. Es folgte eine Erforschung und Erfassung der Insel Pretanz und der anliegenden Inseln. Im Jahr 48 ndZ wurden die Inseln schon fest befahren und bewohnt, einzelne Bauernh&ouml;fe, weit voneinander abgeschottet, behinderten jedoch die Kommunikation und damit die Fortentwicklung des jungen Reiches. Schon zu dieser Zeit bildete sich in Pretannica eine Elite, deren Nachkommen noch heutzutage gr&ouml;&szlig;tenteils die Politik beherrschen, diese einflussreichen Familien und ihre Abk&ouml;mmlinge werden volkst&uuml;mlich tay:klea:vanae, also die gro&szlig;en Nachkommen, genannt. <br />
&lt;p&gt;<br />
&lt;b&gt;Die Zeit der ersten Reichserweiterung&lt;/b&gt;&lt;br&gt;<br />
Einige Zeit sp&auml;ter, im Jahre 152 ndZ, entdeckte der drakische Forscher Vlinium Treyflynn die malazische Inselkette. Das entdeckte Gebiet wurde ins Reich eingegliedert, unwissend &uuml;ber die Gr&ouml;&szlig;e oder die Bev&ouml;lkerung des malazischen Ringes. So geriet es zu ersten gewaltsamen Konflikten, als die auf Malazien lebenden Elfen sich weigerten, ihre althergebrachten Hierarchien aufzugeben und sich dem fremden Volk zu unterwerfen. Wunden dieser Kriege ziehen sich bis in die heutige Zeit. Der Status Malaziens war lange Zeit unklar, das Thema wurde verschwiegen und Einigungen sowie Regelungen konnten h&auml;ufig nur mit Bestechungen an Elfenk&ouml;nige erreicht werden. Dieses Netz aus Korruption und Verheimlichung f&uuml;hrte zum Streit zwischen den einzelnen Elfenv&ouml;lkern, eine Tatsache, die den Einfluss Pretannicas nur noch st&auml;rken konnte. Als letzten Endes Malazien 213 ndZ unter dem jungen Kaiser Dorius als gleichwertige Provinz neben Pretanz im K&ouml;nigreich des Drachen mit der Besiegelung des Alzartiner Abkommens akzeptiert wurde, konnte sich das Land wieder aufbauen, die symbolische Vers&ouml;hnung zwischen den V&ouml;lkern fand durch die Verm&auml;hlung Dorius&#039; mit der Elfenf&uuml;rstin Awleneya statt. <br />
Das K&ouml;nigreich des Drachen bl&uuml;hte unter Kaiser Dorius wirtschaftlich auf und besa&szlig; schon eine beachtenswerte Gr&ouml;&szlig;e, die dem Land Reichtum und den gro&szlig;en Nachkommen Einfluss und Wohlstand brachten. Erste Ans&auml;tze der drakischen Sprache sind um diese Zeit zu suchen, vermutlich stammt das Urdrakische (Draywin) aus dieser Zeit. <br />
Als jedoch Kaiser Dorius um 360 ndZ verstarb und Kaiser Wisgun den Thron bestieg, erlebte das Reich einen rasanten Kurswechsel: Der Kaiser investierte vermehrt in geographische Vermessungen und Landerforschungen, besessen vom Gedanken, in s&uuml;dlicher Richtung gro&szlig;en Reichtum und mehr Macht erlangen zu k&ouml;nnen. Vermehrt hatten Seefahrer von gef&auml;hrlichen Riffen s&uuml;dlich des malazischen Ringes berichtet und Wisgun, &uuml;berzeugt vom Gedanken, in der N&auml;he eines Riffes m&uuml;sse sich Land befinden, sendete eine gro&szlig;e Forschungsflotte aus, um dieses Land im S&uuml;den zu finden. &Uuml;ber die genaue Gr&ouml;&szlig;e der Flotte gibt es keine zuverl&auml;ssigen Angaben, aufgrund der sonst sehr genauen Chroniken vermutet man jedoch, dass Wisgun gro&szlig;e Teile der Flotte erst am Staatshaushalt vorbei durch Bestechungen finanzieren konnte. Im Jahr 637 ndZ best&auml;tigte sich dann Wisguns These: Die Forschungsschiffe trafen auf die nord&ouml;stliche Nase, den n&ouml;rdlichsten Punkt Pelatas. Die Eingliederung Pelatas, die Loyalit&auml;tsschw&ouml;rungen der umherziehenden Nomadenf&uuml;rsten, gestaltete sich einfach. Umso schwerer erschien allerdings die Erforschung dieser neuen Welt: Nahezu undurchdringliche Urw&auml;lder im S&uuml;den, endlose Steppen im Norden und sengende W&uuml;sten dazwischen lie&szlig;en lange Zeit eine genaue Kartographierung nicht zu, lediglich die Region des Owezylan (Owez-Fluss) wurde als Lebensader des neuen Landes gut erforscht. <br />
&lt;p&gt;<br />
&lt;b&gt;Aufbl&uuml;hen des Reiches&lt;/b&gt;&lt;br&gt;<br />
Die zweite H&auml;lfte seiner Regierungszeit herrschte Kaiser Wisgun I. nun also &uuml;ber ein K&ouml;nigreich mit Ausma&szlig;en, die zu Zeiten Martinus&#146; I. noch undenkbar gewesen w&auml;ren. Der Einfluss des Hofes von Pretannica wuchs mit der Zeit, m&auml;chtige Bauten demonstrierten die erste Bl&uuml;te der Stadt, die inzwischen 40 000 Einwohner erreicht hatte. Wisgun I. betrachtete den Bau eines Schlosses anstelle der Festung f&uuml;r notwendig, um die Gr&ouml;&szlig;e des Reiches zu demonstrieren. Die Vorderfront des Schlosses, auf dem opulenten Martinusplatz geplant zusammen mit der Hauptverwaltung der Kaiserlichen Bank von Drachenstein, der Schatzkammer des Reiches, und der Kathedrale zu Winston in der ersten Ausbaustufe als Symbol des Glaubens, wurde Wisgun I. schlie&szlig;lich zum Verh&auml;ngnis: Er wurde 1187 ndZ abgesetzt, zehn drakische Jahre nach Beginn der Bauarbeiten am Schloss, als die tats&auml;chlichen hohen Kosten des Bauvorhabens zum Vorschein kamen, die er bis dahin geschickt kaschieren konnte. Kaiser Wisgun I. wurde daraufhin ins Exil nach Krimez gebracht, wo er eine Festung als Ersatz f&uuml;r sein Schloss sowie das heute noch f&uuml;r die Sommerfrische von der Kaiserfamilie genutzte Kaiserpalais bauen lie&szlig;. Als dann der Sohn Wisguns I., Tribald I., 1192 ndZ zum K&ouml;nig gekr&ouml;nt wurde, ver&auml;nderte sich der Forschungskurs der Politik wieder zu Gunsten der Wirtschaft: Handlungsverbindungen wurden geschaffen, die der Reichshauptstadt eine Versorgung mit seltenen G&uuml;tern garantierten und die Hauptst&auml;dte der Provinzen Malazien und Pelata, die damals in direkter Weise Pretanz unterstellt waren, an den Mittelpunkt des K&ouml;nigreiches binden sollte. Die F&uuml;rsten dieser Provinzen, eingesetzt vom K&ouml;nig des Reiches und damit oftmals lediglich Marionetten, wehrten sich aufgrund hoher Zahlungen und gesellschaftlicher Anerkennung kaum gegen diese Repressionspolitik. Das Volk jedoch erkannte den offensichtlichen Regierungskurs und erste Zellen zur Bek&auml;mpfung der Abh&auml;ngigkeit der Provinzen von Pretanz bildeten sich. Es sollte jedoch &uuml;ber sechstausend drakische Jahre dauern, bis diese Zellen Geh&ouml;r bei der breiten Bev&ouml;lkerung fanden und tiefgreifende Ver&auml;nderungen bewirken konnten, die das K&ouml;nigreich des Drachen endg&uuml;ltig zerst&ouml;ren sollten. <br />
&lt;p&gt;<br />
&lt;b&gt;Die Zeit der zweiten Reichserweiterung&lt;/b&gt;&lt;br&gt;<br />
Ein erstes Opfer dieser Zellen, voran die Schwarze Hand und die Independum Drakis, wurde Tribald I.: W&auml;hrend der Weihungszeremonien 1612 ndZ zum unvollendeten Hauptteil der Kathedrale zu Winston, womit ein wichtiges Erbe seines Vaters erf&uuml;llt werden sollte, erschoss ein unbekannter Armbrustsch&uuml;tze vom Geb&auml;lk des Zerronturms aus den Kaiser. Die Zeremonie wurde &uuml;berst&uuml;rzt abgebrochen, hohe Beamte in Sicherheit gebracht, jedoch konnte der T&auml;ter nicht ausfindig gemacht werden. Lediglich der Abdruck einer Hand mit schwarzer Farbe im neuen Sandstein der Kathedrale deuteten auf die Schwarze Hand hin. Der Abdruck soll sich noch heute dort befinden, da der tragende Stein mit dem Handabdruck nicht mehr ohne erhebliche Kosten ausgewechselt werden konnte. Tribald I. hinterlie&szlig; vier S&ouml;hne, der &auml;lteste Sohn, Varion I., wurde nach kurzer Zeit 1613 in der Kathedrale zu Winston zum K&ouml;nig gekr&ouml;nt, gleichzeitig mit der Kr&ouml;nigszeremonie erfolgte die Weihung der Kathedrale, bei der sein Vater ermordet worden war. Varion I. versuchte, den starken Forschungskurs seines Gro&szlig;vaters mit dem Streben nach Wirtschaft seines Vaters zu vereinen: Es waren Ger&uuml;chte &uuml;ber Land im Westen aufgekommen, die Varion I. allzugern best&auml;tigen w&uuml;rde, um schon in jungen Herrschaftsjahren seinen Stand zu festigen. Seine Missionen schlugen jedoch bis zu seinem jungen Tod im Jahr 1932 ndZ fehl, sein j&uuml;ngerer Bruder und folgender K&ouml;nig, Pleiron I., setzte seine Bem&uuml;hungen danach erfolgreich fort, als er den Ansatz verfolgte, nicht westlich von Pelata, sondern weiter n&ouml;rdlich, westlich des Malazischen Ringes, zu suchen. Die neu entdeckte Provinz wurde Vincaster genannt und fand mit seinem milden Klima und der h&uuml;geligen, ruhigen Landschaft schnell gro&szlig;e Forscher, die das Land durchquerten und erforschten. Vintor wurde als Hauptstadt der neuen Provinz gegr&uuml;ndet und diente fortan als entlegener Au&szlig;enposten des K&ouml;nigreichs. Nicht weit von Vintor entfernt befand sich die Pisar-Landbr&uuml;cke, benannt nach dem Forscher Dirion Pisar. Sie stellte gro&szlig;e R&auml;tsel auf, man vermutete zwar eine einfache K&uuml;stenzunge, jedoch hob sich das Land dort an. Dieses R&auml;tsel wurde erst unter dem darauffolgenden Kaiser, Darisin I., gel&ouml;st: Die sogenannte Expedition des Hohen Meeres durchquerte Vincaster auf geradem Westkurs, gelangte zur Pisar-Landbr&uuml;cke und forschte auf bisherigem Kurs weiter. Das Ergebnis, das bei der R&uuml;ckkehr der Expedition nach Vintor im Jahr 2482 ndZ verk&uuml;ndet werden konnte, war verbl&uuml;ffend: Ein riesiges, unentdecktes Hochland mit steilen Schluchten, verschneiten H&auml;ngen und brodelnden Vulkanen erstreckte sich westlich des bekannten K&ouml;nigreichs. Als Darisin I. von diesen Erkenntnissen h&ouml;rte, t&auml;tigte er angeblich den ber&uuml;hmten Ausspruch: Wie ist die Welt so gro&szlig;! Er&ouml;ffnete Arme erwarten unsere Erforschung im unendlichen Meer., der das damalige Befinden wiederspiegelt, wonach man aufgrund der zahlreichen Entdeckungen vermutete, die Welt sei ein unendliches Meer, in dem die Inseln treiben w&uuml;rden. Pisar wurde zur f&uuml;nften Provinz des K&ouml;nigreichs erkl&auml;rt, entwickelte sich jedoch aufgrund der entfernten Lage und der harten Lebensbedingungen eher zaghaft. <br />
&lt;p&gt;<br />
&lt;b&gt;Ausrufung des Ersten Kaiserreiches&lt;/b&gt;&lt;br&gt;<br />
Die gro&szlig;en Landmassen, die nun zum K&ouml;nigreich des Drachen geh&ouml;rten, st&auml;rkten ein neues Nationalgef&uuml;hl: Das K&ouml;nigreich sah sich nicht mehr als notwendigen Verwaltungsarbeit, um die Territorien zu verwalten, sondern als vereintes Land unter der Herrschaft des gottgleichen K&ouml;nigs. Die lange Tradition der K&ouml;nige, der aufkommende Martinuskult und die St&auml;rkung der Runischen Religion veranlassten den damaligen K&ouml;nig Solvin I. zu einem wichtigen Schritt: Er beschloss, grundlegende Ver&auml;nderungen in der Strukturierung des Reiches f&uuml;r gr&ouml;&szlig;ere Effektivit&auml;t und f&uuml;r eine neue Herrlichkeit des Reiches zu schaffen und erschaffte das Erste Kaiserreich, erstmals mit dem Namen Kaiserreich Drachenstein, ein Nachfolgerstaat des m&auml;chtigen K&ouml;nigreich des Drachen, womit er alle bisherigen K&ouml;nige als Nachfolger des heiligen Martinus in den Stand eines Kaisers erhob. Die Provinzen bekamen als Aufwertung je einen vom Kaiser ernannten K&ouml;nig, wobei der Kaiser Drachensteins der K&ouml;nig Pretanz&#146; sein sollte. Damit wurde die Position der Provinz Pretanz als Reichsmittelpunkt gest&auml;rkt, als Regierungssitz und Hauptstadt wurde Pretannica festgelegt. Die K&ouml;nige tagten st&auml;ndig im Parliatium, wo sie das Reich leiteten und lenkten, ein gemeinsames Auftreten sollte die Gemeinschaft des Kaiserreiches zeigen und verhindern, dass man von einer Lenkung Drachensteins von Pretannica aus sprechen konnte. Die Zeit unter Kaiser Solvin verlief friedlich und das Kaiserreich erbl&uuml;hte unter seiner Hand zu neuen St&auml;rken, die Herrschaft des beliebten Kaisers verlief fast dreitausendf&uuml;nfhundert drakische Jahre, in denen eine Hochkultur und ein Organisationsnetzwerk entstanden, die seither das Reich pr&auml;gen. <br />
&lt;p&gt;<br />
&lt;b&gt;Der Silusische Krieg&lt;/b&gt;&lt;br&gt;<br />
Als im Jahr 7362 ndZ Kaiser Solvin I. verstarb, schien das Reich zusammenzubrechen: Gro&szlig;e Unruhen traten zu Tage, Probleme, die sich w&auml;hrend der vergangenen dreitausendf&uuml;nfhundert Jahre angestaut hatten und durch die allgemeine Euphorie unterdr&uuml;ckt gehalten worden waren, kamen pl&ouml;tzlich auf. Schnellstm&ouml;glich wurde der Nachfolger, Wisgun II., gekr&ouml;nt. Er vermochte es jedoch nicht, die V&ouml;lker wieder zusammenzuf&uuml;hren, und so begann das Reich, auseinanderzubrechen. Den Anfang machte 7379 ndZ Pelata, das, gef&uuml;hrt von der Vereinigung Independum Drakis, seine Unabh&auml;ngigkeit von Drachenstein erkl&auml;rte und unter dem bisherigen K&ouml;nig Derenovius ein eigenst&auml;ndiges K&ouml;nigreich bildete. Pretanz weigerte sich, die Unabh&auml;ngigkeit Pelatas zu akzeptieren und erkl&auml;rte den Rebellen den Krieg. Die wahre Gr&ouml;&szlig;e der &Uuml;berl&auml;ufer der Drakischen Armee konnte jedoch nicht abgesehen werden und so kam es, dass die ausgesendete Schiffsflotte in Rosener, der Hauptstadt Pelatas, unter Jubel mit gehissten Pelatischen Flaggen einzog, um sich dem neuen K&ouml;nigreich anzuschlie&szlig;en. Pretanz erschien machtlos und so wagte es Malazien, sich von diesem schwachen Kern loszusagen. Das K&ouml;nigreich Pelata und das K&ouml;nigreich Malazien nahmen sofort Kontakt zueinander auf, vereinigten sich zum Silusischen Bund und besprachen weitere Taktiken. Pretanz war vom Malazischen Ring eingeschlossen und hatten keine Hoffnung auf regelm&auml;&szlig;igen Kontakt mit den verbleibenden Provinzen Vincaster und Pisar. Eine letzte Armee Pretanz&#146; versuchte, sich einen Weg durch Malazien zu bahnen, die Truppen scheiterten jedoch mit hohen Verlusten an den H&auml;ngen der Malazischen Bergkette, im Schnee von pelatischen Bogensch&uuml;tzen &uuml;berrascht und, durch tagelangen Marsch und das kalte Wetter geschw&auml;cht, vernichtet. Nun entsagte Vincaster Pretanz und erkl&auml;rte sich zum unabh&auml;ngigen K&ouml;nigreich, mit Pelata und Malazien vereinigt entstand der Gro&szlig;silusische Bund. Pisar, nun vollkommen von Pretanz getrennt, hielt jedoch weiterhin die Stellung und konnte erst durch einen Vormarsch durch das K&ouml;nigreich Vincaster erobert und diesem angeschlossen werden, womit Pretanz als letzte Bastion des Kaiserreiches Drachenstein verblieb. Ab diesem Zeitpunkt, 7504 ndZ, spricht man allgemein vom Silusischen Krieg oder Stillen Krieg, wie er auch genannt wird. Im W&uuml;rgegriff Malaziens hielt Pretanz am Kaiserreichsgedanken fest und erkannte weiterhin nicht den Gro&szlig;silusischen Bund an. Kaiser blieb Wisgun II., wobei das allt&auml;gliche und politische Leben durch Anschl&auml;ge und Drohungen der Independum Drakis erschwert wurde. So kam es, dass 7566 ndZ Wisgun II. nach einem Leben in st&auml;ndiger Gefahr f&uuml;r Vampire sehr fr&uuml;h verstarb, eine Tatsache, die jedoch viel Blutvergie&szlig;en und schlimme Jahre des Stillen Krieges ersparten. Der Nachfolger Veuxin I. erkannte den Gro&szlig;silusischen Bund an und beendete somit den Silusischen Krieg. Durch geschickte Verhandlungen konnte er Pisar zur&uuml;ck an das Kaiserreich angliedern, die Ordnung, die vorher in beiden Provinzen durch chaotische B&uuml;rgerkriegszust&auml;nde abgel&ouml;st worden war, wurde unter dem neuen Kaiser wieder hergestellt. Die entstehende Paradefunktion veranlasste die unzufriedene Bev&ouml;lkerung Malaziens 7582 ndZ, ihren Herrscher zu st&uuml;rzen und sich wieder dem Kaiserreich anzuschlie&szlig;en. Vincaster folgte nur f&uuml;nf drakische Jahre sp&auml;ter, Pelata klammerte sich noch weitere drei&szlig;ig drakische Jahre an den nicht mehr vorhandenen Gro&szlig;silusischen Bund, bis es sich 7617 ndZ nach Zahlung erheblicher Geldsummen wieder dem Kaiserreich Drachenstein beitrat. <br />
&lt;p&gt;<br />
&lt;b&gt;Die Nachkriegszeit&lt;/b&gt;&lt;br&gt;<br />
Die folgende Epoche unter der Herrschaft von Veuxin I., obwohl mit 4376 Jahren keinesfalls kurz, wird allgemein als Nachkriegszeit bezeichnet. Geschw&auml;cht durch die Erlebnisse des Krieges fing das Reich erst langsam an, sich aufzurichten. Ein neues Kulturgef&uuml;hl begann sich zu entwickeln, das etwa im Imperialen Stil Ausdruck fand (siehe Architektur). Man begann, die neue alte Identit&auml;t des Volkes der Drachen zu entdecken und berief sich auf seine Wurzeln. Gro&szlig;e Balladen und Heldenepen entstanden in dieser Zeit, ab etwa 8300 begann sich der neulateinische Stil, der sich auf den angeblichen Stil Martinus&#039; I. st&uuml;tzen sollte, zu entwickeln. Der Stil zeichnete sich durch wallende, meist nur aus einem Tuch bestehenden Gew&auml;nder, bunt angemalte Hausfassaden und &uuml;ppigen Blattgoldeinsatz aus. In der Literatur kamen sogenannte Landhauserz&auml;hlungen auf, die vom Leben der fr&uuml;hen Bewohner Drachensteins berichteten, sich auf die einfachen Dinge des Lebens konzentrierten und im Streben nach Einfachkeit das Gl&uuml;ck finden wollten. Die Musik dieser Zeit ist durch einfache Melodien gepr&auml;gt, die mit Hausmannsinstrumenten &ndash;&nbsp;Geige, Hirtenfl&ouml;te, Laute, vaylmer (Windwort, ein orgel&auml;hnliches, tragbares Instrument) und Schellen &ndash;&nbsp;gespielt oder begleitet werden. Dabei wurde jedoch keineswegs vor &Uuml;bertreibung gesch&uuml;tzt: Ein Lautenkonzert des drakischen Komponisten Fenno Kesmah hatte eine Besetzung von dreiundzwanzig Lauten, achtundzwanzig Fl&ouml;ten, siebzehn Geigen und acht Kesselpauken f&uuml;r die rhythmische Untermalung. Diese &Auml;ra des Friedens, wirtschaftlichen Aufbl&uuml;hens und wissenschaftlichen Fortschritts hielt w&auml;hrend der Regierungszeiten von Limun I., Terioin I. und Tribald II. an. <br />
&lt;p&gt;<br />
&lt;i&gt;Weiteres in Arbeit&lt;/i&gt;<br />
&lt;p&gt;<br />
&lt;b&gt;Die Zeit Esturien-Drachensteins&lt;/b&gt;&lt;br&gt;<br />
Am 22. Termophles 24022 (22.08.2005) unterzeichneten Erzregent Kristofer da Ripat und Kaiser Veuxin II. von Drachenstein in der Burg Estoria in Esturien den historischen Vertrag, der Esturien und Drachenstein zum Kaiserreich Esturien-Drachenstein vereinigte. Dieser Schritt wurde als notwendig empfunden, als in Esturien aufgrund des spurlosen Verschwindens von Sinister Don Rumata LXII. von Esturien Anarchie auszubrechen drohte. Die Verfassung des Kaiserreichs Esturien-Drachenstein wurde dann am 9. Kaltorles 24022 (09.10.2005) ratifiziert und die Verfassungsgebende Nationalversammlung aufgel&ouml;st. Am 22. Termophles 24023 jedoch wurde die Verfassung des Kaiserreichs Esturien-Drachenstein wieder ge&auml;ndert, das Kaiserreich zur&uuml;ck in Drachenstein umbenannt und Esturien in Drachenstein als Provinz eingegliedert. Damit endete das zweimonatige Bestehen des Kaiserreichs Esturien-Drachenstein. Das Kaiserreich Drachenstein konnte dann am 9. Kaltorles 24032, genau zehn Jahre nach der Schaffung des Kaiserreichs Esturien-Drachenstein, erneut Zuwachs verzeichnen: Das K&ouml;nigreich Arldroy neben Esturien wurde durch Verfassungs&auml;nderung die siebte Provinz Drachensteins. </p>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
&raquo; <a href="http://drakestrin.de/encyclopedia">Kompendium</a> &rarr; <a href="http://drakestrin.de/encyclopedia/view/2">Allgemeines</a> &rarr; Geschichte</p>
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