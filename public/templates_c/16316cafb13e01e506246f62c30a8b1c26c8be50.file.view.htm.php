<?php /* Smarty version Smarty-3.1.5, created on 2013-06-03 02:57:18
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/user/view.htm" */ ?>
<?php /*%%SmartyHeaderCode:14284364265126eb866a3339-29840179%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
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
  'nocache_hash' => '14284364265126eb866a3339-29840179',
  'function' => 
  array (
    'datetime' => 
    array (
      'parameter' => 
      array (
        'objvalue' => NULL,
      ),
      'compiled' => '',
    ),
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5126eb8688cb5',
  'variables' => 
  array (
    'config' => 0,
    'user' => 1,
    'notice' => 1,
    'i' => 1,
    'online' => 1,
    'value' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5126eb8688cb5')) {function content_5126eb8688cb5($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: <?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_css/user_id.css" />
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
		<h2><?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
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
		
<?php /*  Call merged included template "../_function/datetime.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/datetime.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '14284364265126eb866a3339-29840179');
content_51abe9ee40a54($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/datetime.htm" */?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->avatar){?><img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/user_avatar.id/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
.jpg" /><?php }?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->usertext){?><p id="usertext"><?php echo nl2br(mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->usertext, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8'));?>
</p><?php }?>
<ol class="columns">
	<li><h4>Aktivit&auml;t</h4>
		<ol>
			<li><em>Mitglied seit:</em> <?php smarty_template_function_datetime($_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['obj']->value->regdate));?>
</li>
			<li><em>Letzter Besuch:</em> <?php smarty_template_function_datetime($_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['obj']->value->lastvisit));?>
</li>
			<li><em>Beitr&auml;ge insgesamt:</em> <?php echo $_smarty_tpl->tpl_vars['obj']->value->post__total;?>
</li>
			<li><em>Beitr&auml;ge pro Tag:</em> <?php echo round(($_smarty_tpl->tpl_vars['obj']->value->post__total/((time()-$_smarty_tpl->tpl_vars['obj']->value->regdate)/86400)),"2");?>
</li>
		</ol>
	</li>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->group){?>
	<li><h4>Gruppen</h4>
		<ol>
<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->group; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value){
$_smarty_tpl->tpl_vars['group']->_loop = true;
?>
			<li><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/group/view/<?php echo $_smarty_tpl->tpl_vars['group']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value->name;?>
</a></li>
<?php } ?>
		</ol>
	</li>
<?php }?>
	<li><h4>Informationen<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option edit" title="editieren">editieren</a><?php }?></h4>
		<ol>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->gender>0){?>
			<li><em>Geschlecht:</em> <?php if ($_smarty_tpl->tpl_vars['obj']->value->gender==1){?>m&auml;nnlich<?php }else{ ?>weiblich<?php }?></li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->birthday>0){?>
			<li><em>Geburtstag:</em> <?php echo $_smarty_tpl->tpl_vars['obj']->value->birthday;?>
</li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->location){?>
			<li><em>Herkunft:</em> <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->location, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->interests){?>
			<li><em>Interessen:</em> <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->interests, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</li>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->work){?>
			<li><em>Amt oder Beruf:</em> <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->work, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</li>
		<?php }?>
		</ol>
	</li>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->user_contact||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?>
	<li><h4>Kontakt<?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/createcontact/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="neue Kontaktm&ouml;glichkeit hinzuf&uuml;gen">neue Kontaktm&ouml;glichkeit hinzuf&uuml;gen</a><?php }?></h4>
		<?php if ($_smarty_tpl->tpl_vars['obj']->value->user_contact){?>
		<ol>
	<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->user_contact; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
			<li><em><?php echo $_smarty_tpl->tpl_vars['value']->value->protocol->name;?>
:</em> <?php if ($_smarty_tpl->tpl_vars['value']->value->protocol->link){?><a href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['value']->value->protocol->link,'\\1',$_smarty_tpl->tpl_vars['value']->value->contact);?>
"><?php echo $_smarty_tpl->tpl_vars['value']->value->contact;?>
</a><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['value']->value->contact;?>
<?php }?><?php if (($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==2||($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'edituser')==1&&$_smarty_tpl->tpl_vars['user']->value->id==$_smarty_tpl->tpl_vars['obj']->value->id))){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/editcontact/<?php echo $_smarty_tpl->tpl_vars['value']->value->id;?>
" class="option edit" title="Kontaktm&ouml;glichkeit bearbeiten">Kontaktm&ouml;glichkeit bearbeiten</a> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/deletecontact/<?php echo $_smarty_tpl->tpl_vars['value']->value->id;?>
" class="option delete" title="Kontaktm&ouml;glichkeit l&ouml;schen">Kontaktm&ouml;glichkeit l&ouml;schen</a><?php }?></li>
	<?php } ?>
		</ol>
		<?php }?>
	</li>
<?php }?>
</ol>

<?php if ($_smarty_tpl->tpl_vars['obj']->value->inventory___owner){?>
<h3>Besitz</h3>
<ol class="inventory">
<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->inventory___owner; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
	<li><img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/item.img/<?php echo $_smarty_tpl->tpl_vars['value']->value->item->img;?>
.png" /> <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->item->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
: <?php echo $_smarty_tpl->tpl_vars['value']->value->stack;?>
, <?php echo $_smarty_tpl->tpl_vars['value']->value->wear;?>
</li>
<?php } ?>
</ol>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->company){?>
<h3>Betriebe</h3>
<ol>
<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->company; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
	<li><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
: <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->description, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</li>
<?php } ?>
</ol>
<?php }?>

		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a>
 &raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user">Mitglieder</a> &rarr; <?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
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
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-06-03 02:57:18
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/datetime.htm" */ ?>
<?php if ($_valid && !is_callable('content_51abe9ee40a54')) {function content_51abe9ee40a54($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><?php if (!function_exists('smarty_template_function_datetime')) {
    function smarty_template_function_datetime($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['datetime']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
<?php if ($_smarty_tpl->tpl_vars['value']->value>=mktime(0,0,0)){?><strong>heute,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['objvalue']->value,'time'));?>

<?php }elseif($_smarty_tpl->tpl_vars['value']->value>=(mktime(0,0,0)-86400)){?><strong>gestern,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['objvalue']->value,'time'));?>

<?php }elseif($_smarty_tpl->tpl_vars['value']->value>=(mktime(0,0,0)-604800)){?><strong><?php if (smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==1){?>Montag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==2){?>Dienstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==3){?>Mittwoch<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==4){?>Donnerstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==5){?>Freitag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==6){?>Samstag<?php }elseif(smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,"%w")==0){?>Sonntag<?php }?>,</strong> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['objvalue']->value,'time'));?>

<?php }else{ ?><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['objvalue']->value,'date'));?>
, <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['value']->value,$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['objvalue']->value,'time'));?>
<?php }?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php }} ?>