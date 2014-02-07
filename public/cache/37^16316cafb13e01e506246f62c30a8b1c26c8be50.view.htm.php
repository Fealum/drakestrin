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
    'e63dcfbbaab81db0fcf45d6a6859095939eb9255' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_notice/user_edit.htm',
      1 => 1369399533,
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
  'unifunc' => 'content_52f3488638c69',
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
<?php if ($_valid && !is_callable('content_52f3488638c69')) {function content_52f3488638c69($_smarty_tpl) {?><?php $_smarty = $_smarty_tpl->smarty; if (!is_callable('smarty_modifier_replace')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Kaiserreich Drachenstein: Veuxin ent Drakestrin-Rumata</title>
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
		<h2>Veuxin ent Drakestrin-Rumata</h2>
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
		
<img src="http://drakestrin.de/img/user_avatar.id/37.jpg" /><p id="usertext">&mdash; Honneur Le Genre Noble &mdash;</p><ol class="columns">
	<li><h4>Aktivit&auml;t</h4>
		<ol>
			<li><em>Mitglied seit:</em> 28.05.2003, 15:42</li>
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
			<li><a href="http://drakestrin.de/group/view/2">Administrator</a></li>
		</ol>
	</li>
	<li><h4>Informationen<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="editieren">editieren</a><?php }?></h4>
		<ol>
					<li><em>Geschlecht:</em> m&auml;nnlich</li>
							<li><em>Geburtstag:</em> 432</li>
							<li><em>Herkunft:</em> Pretannica</li>
							<li><em>Interessen:</em> Reiten, Schwertkampf, Schach spielen, Astronomie, Lesen</li>
							<li><em>Amt oder Beruf:</em> Kaiser von Drachenstein</li>
				</ol>
	</li>
	<li><h4>Kontakt<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/createcontact/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="neue Kontaktm&ouml;glichkeit hinzuf&uuml;gen">neue Kontaktm&ouml;glichkeit hinzuf&uuml;gen</a><?php }?></h4>
				<ol>
				<li><em>ICQ:</em> <a href="http://www.icq.com/people/283991769/">283991769</a><?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="http://drakestrin.de/user/editcontact/71" class="option edit" title="Kontaktm&ouml;glichkeit bearbeiten">Kontaktm&ouml;glichkeit bearbeiten</a> <a href="http://drakestrin.de/user/deletecontact/71" class="option delete" title="Kontaktm&ouml;glichkeit l&ouml;schen">Kontaktm&ouml;glichkeit l&ouml;schen</a><?php }?></li>
				<li><em>Website:</em> <a href="http://drakestrin.de/">http://drakestrin.de/</a><?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="http://drakestrin.de/user/editcontact/11" class="option edit" title="Kontaktm&ouml;glichkeit bearbeiten">Kontaktm&ouml;glichkeit bearbeiten</a> <a href="http://drakestrin.de/user/deletecontact/11" class="option delete" title="Kontaktm&ouml;glichkeit l&ouml;schen">Kontaktm&ouml;glichkeit l&ouml;schen</a><?php }?></li>
				<li><em>Skype:</em> Veuxin<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="http://drakestrin.de/user/editcontact/10" class="option edit" title="Kontaktm&ouml;glichkeit bearbeiten">Kontaktm&ouml;glichkeit bearbeiten</a> <a href="http://drakestrin.de/user/deletecontact/10" class="option delete" title="Kontaktm&ouml;glichkeit l&ouml;schen">Kontaktm&ouml;glichkeit l&ouml;schen</a><?php }?></li>
			</ol>
			</li>
</ol>

<h3>Besitz</h3>
<ol class="inventory">
	<li><img src="http://drakestrin.de/img/item.img/4.png" /> Kaiserkrone: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/5.png" /> Amulett der Kaiser: 0, 1</li>
	<li><img src="http://drakestrin.de/img/item.img/24.png" /> Einfaches Messer: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/54.png" /> K&ouml;cher: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/49.png" /> Genauer Pfeil: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/49.png" /> Genauer Pfeil: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/53.png" /> Goldener Prunkharnisch: 0, 1</li>
	<li><img src="http://drakestrin.de/img/item.img/97.png" /> Gr&uuml;ner Seidenschal: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/152.png" /> Kaiserlicher Zeremonienumhang: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/162.png" /> Rothirschkuh: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/7.png" /> Einfaches Hemd: 0, 1</li>
	<li><img src="http://drakestrin.de/img/item.img/9.png" /> Einfache Hose: 0, 1</li>
	<li><img src="http://drakestrin.de/img/item.img/184.png" /> Kutte des Gottes Parn: 0, 1</li>
	<li><img src="http://drakestrin.de/img/item.img/90.png" /> Honigkekse: 0, 0</li>
	<li><img src="http://drakestrin.de/img/item.img/69.png" /> Schwarzbrot: 0, 0.03</li>
	<li><img src="http://drakestrin.de/img/item.img/69.png" /> Schwarzbrot: 1, 0</li>
</ol>
<h3>Betriebe</h3>
<ol>
	<li>Grifftens Taverne: Saufet, Ihr, der Gottheit wilde Meute! </li>
	<li>Northshire-Teehaus: Das kleine Teehaus von Northshire, geeignet f&uuml;r lange Diskussionen und kurzweilige Gespr&auml;che</li>
	<li>Wildnisleben: Ein Laden der Wanderergilde f&uuml;r alles, was das Herz des Wanderers h&ouml;her schlagen l&auml;sst! </li>
	<li>Kaiserlich Drakisches Scriptorium: Im Kaiserlich Drakischen Scriptorium werden Schreibauftr&auml;ge jeglicher Art ausgef&uuml;hrt. </li>
	<li>Kaiserlich Drakische Bank: Die Kaiserlich Drakische Bank verwahrt und verleiht Geld und deportiert Wertgegenst&auml;nde. </li>
	<li>Imanum &raquo;trifolior auris&laquo;: Die Imkerei &raquo;trifolior auris&laquo; stellt neben zahlreichen Imkereiprodukten wie Kerzen oder Honig den ber&uuml;hmten Vierklee-Met her. </li>
	<li>Hangerwye-K&auml;serei: Die alte Hangerwye-K&auml;serei im Osten von Greenhampton stellt seit jeher Milch und K&auml;se her</li>
	<li>Kirchenverwaltung: Dieser Betrieb verwaltet die runische Kirche, ihre Ausgaben und ihre Einnahmen. </li>
	<li>Llamez-Goldminen: Die Llamez-Goldminen holen reinstes Feingold aus dem Erdreich</li>
	<li>Forstamt Pretanz: Das Forstamt Pretanz verwaltet einen Teil der heimischen W&auml;lder und erntet Holz daraus, welches an S&auml;gem&uuml;hlen verkauft werden kann. </li>
	<li>Leminias Bauernhof: Der Bauernhof des alten Leminia baut Getreide an. </li>
	<li>Stockwich-K&ouml;hlerei: Die K&ouml;hlerei von Stockwich</li>
</ol>

		</div>
		<div id="sidebar">
			<p><a href="http://drakestrin.de/">Kaiserreich Drachenstein</a>
 &raquo; <a href="http://drakestrin.de/user">Mitglieder</a> &rarr; Veuxin ent Drakestrin-Rumata</p>
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