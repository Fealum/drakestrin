<?php /* Smarty version Smarty-3.1.5, created on 2013-05-26 10:17:04
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/group/view.htm" */ ?>
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
  'function' => 
  array (
    'bit_user' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '
<?php /*  Call merged included template "../_function/datetime.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate(\'../_function/datetime.htm\', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0, \'84594107751a1c5000865c9-96652221\');
content_51a1c50018a00($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/datetime.htm" */?>
<li>
	<img src="<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/img/user_avatar.id/thumb/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->avatar){?><?php echo $_smarty_tpl->tpl_vars[\'value\']->value;?>
<?php }else{ ?>0<?php }?>.jpg" />
	<a href="<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
</a> 
	<p class="small"><?php echo $_smarty_tpl->tpl_vars[\'value\']->value->post__total;?>
 Beitr&auml;ge seit <?php Smarty_Internal_Function_Call_Handler::call (\'datetime\',$_smarty_tpl,array(\'value\'=>$_smarty_tpl->tpl_vars[\'value\']->value->regdate),\'84594107751a1c5000865c9_96652221\',false);?>
</p>
</li>',
      'nocache_hash' => '84594107751a1c5000865c9-96652221',
      'has_nocache_code' => false,
      'called_functions' => 
      array (
        0 => 'datetime',
      ),
    ),
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
      'nocache_hash' => '84594107751a1c5000865c9-96652221',
      'has_nocache_code' => false,
      'called_functions' => 
      array (
      ),
    ),
  ),
  'variables' => 
  array (
    'config' => 0,
    'user' => 1,
    'notice' => 1,
    'i' => 1,
    'online' => 1,
    'value' => 1,
  ),
  'has_nocache_code' => true,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_51a1c5004a6f9',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51a1c5004a6f9')) {function content_51a1c5004a6f9($_smarty_tpl) {?><?php if (!is_callable('smarty_function_paginate')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/function.paginate.php';
?><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php $_smarty = $_smarty_tpl->smarty; if (!is_callable(\'smarty_modifier_date_format\')) include \'/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php\';
?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: Gruppe &raquo;<?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
&laquo;</title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_css/group_view.css" />
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
	<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if (isset($_smarty_tpl->tpl_vars[\'user\']->value)){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<a id="notifypic" href="#"><img src="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'user\']->value->avatar){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
0<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
.jpg" /></a>
		Sali Vuz, <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value->name;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>! [<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/log/out">abmelden</a>]
	<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		Sali Vuz, Wanderer! [<a class="link" href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/register">registrieren</a>|<a class="link" href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/log">anmelden</a>]
	<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		</div></div>
		<h2>Gruppe &raquo;<?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
&laquo;</h2>
		<div id="content">
		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php  $_smarty_tpl->tpl_vars[\'i\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'i\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'notice\']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'i\']->key => $_smarty_tpl->tpl_vars[\'i\']->value){
$_smarty_tpl->tpl_vars[\'i\']->_loop = true;
?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<p class="notice notice_<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'i\']->value[\'type\'];?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars[\'i\']->value[\'notice\']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</p>
		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php } ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		
<?php /*  Call merged included template "../_function/bit_user.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/bit_user.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0, '84594107751a1c5000865c9-96652221');
content_51a1c500181f2($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/bit_user.htm" */?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->user){?>
<h3>Mitglieder</h3>
<?php echo smarty_function_paginate(array('link'=>($_smarty_tpl->tpl_vars['config']->value->url).("/group/view/".($_smarty_tpl->tpl_vars['obj']->value)),'total'=>ceil((int)count($_smarty_tpl->tpl_vars['obj']->value->user)/$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries')),'cur'=>$_smarty_tpl->tpl_vars['page']->value),$_smarty_tpl);?>

<ol class="users">
<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->user; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['value']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['value']->iteration++;
?>
<?php if ($_smarty_tpl->tpl_vars['value']->iteration>(($_smarty_tpl->tpl_vars['page']->value-1)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))&&$_smarty_tpl->tpl_vars['value']->iteration<=(($_smarty_tpl->tpl_vars['page']->value)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))){?>
<?php Smarty_Internal_Function_Call_Handler::call ('bit_user',$_smarty_tpl,array('config'=>$_smarty_tpl->tpl_vars['config']->value,'value'=>$_smarty_tpl->tpl_vars['value']->value),'84594107751a1c5000865c9_96652221',false);?>

<?php }elseif($_smarty_tpl->tpl_vars['value']->iteration>(($_smarty_tpl->tpl_vars['page']->value)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))){?><?php break 1?><?php }?>
<?php } ?>
</ol>
<?php echo smarty_function_paginate(array('link'=>($_smarty_tpl->tpl_vars['config']->value->url).("/group/view/".($_smarty_tpl->tpl_vars['obj']->value)),'total'=>ceil((int)count($_smarty_tpl->tpl_vars['obj']->value->user)/$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries')),'cur'=>$_smarty_tpl->tpl_vars['page']->value),$_smarty_tpl);?>

<?php }?>

		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a>
 &raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/group">Gruppen</a> &rarr; Gruppe &raquo;<?php echo $_smarty_tpl->tpl_vars['obj']->value->name;?>
&laquo;</p>
			<div id="topoptions"></div>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'online\']->value->data){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			<p id="online">
				Derzeit online: <br />
				<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php  $_smarty_tpl->tpl_vars[\'value\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'value\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'online\']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'value\']->key => $_smarty_tpl->tpl_vars[\'value\']->value){
$_smarty_tpl->tpl_vars[\'value\']->_loop = true;
?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

				<span>
					<img src="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->user->avatar){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
0<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
.jpg" /><a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->user->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a><br />
					<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value->time,$_smarty_tpl->tpl_vars[\'config\']->value->time);?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
:
		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'index\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'std\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
">Index</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'online\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
">Wer ist online?</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Index, Seite <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'board\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
">Foren&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'permissions\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Rechte im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Forum, Seite <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'thread\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'create\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Thema im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/board/view/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo; erstellen
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'delete\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo; l&ouml;schen
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Thema, Seite <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'user\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
">Mitglieder&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a> &laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Mitglieder, Seite <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }else{ ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
Seite <a href="<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
"><?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
/<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>
</a>
		<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

				</span>
				<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php } ?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

			</p>
			<?php echo '/*%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/<?php }?>/*/%%SmartyNocache:84594107751a1c5000865c9-96652221%%*/';?>

		</div>
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-05-26 10:17:04
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/bit_user.htm" */ ?>
<?php if ($_valid && !is_callable('content_51a1c500181f2')) {function content_51a1c500181f2($_smarty_tpl) {?><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-05-26 10:17:04
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/datetime.htm" */ ?>
<?php if ($_valid && !is_callable('content_51a1c50018a00')) {function content_51a1c50018a00($_smarty_tpl) {?><?php }} ?>