<?php /* Smarty version Smarty-3.1.5, created on 2013-05-25 04:39:12
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/view.htm" */ ?>
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
  'function' => 
  array (
    'breadcrumbs' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '
<?php if ($_smarty_tpl->tpl_vars[\'data\']->value->{$_smarty_tpl->tpl_vars[\'parent\']->value}!=\'0\'){?><?php Smarty_Internal_Function_Call_Handler::call (\'breadcrumbs\',$_smarty_tpl,array(\'data\'=>$_smarty_tpl->tpl_vars[\'data\']->value->{$_smarty_tpl->tpl_vars[\'parent\']->value},\'url\'=>$_smarty_tpl->tpl_vars[\'url\']->value,\'parent\'=>$_smarty_tpl->tpl_vars[\'parent\']->value),\'21262246885126d2adedc996_39122634\',false);?>
<?php }?> &rarr; <?php if ($_smarty_tpl->tpl_vars[\'data\']->value->name){?><a href="<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars[\'url\']->value;?>
/<?php echo $_smarty_tpl->tpl_vars[\'data\']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'data\']->value->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
<?php }else{ ?>&hellip;<?php }?></a>',
      'nocache_hash' => '21262246885126d2adedc996-39122634',
      'has_nocache_code' => false,
      'called_functions' => 
      array (
        0 => 'breadcrumbs',
      ),
    ),
    'encyclopedia' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '
<ol>
<?php  $_smarty_tpl->tpl_vars[\'page\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'page\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'data\']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'page\']->key => $_smarty_tpl->tpl_vars[\'page\']->value){
$_smarty_tpl->tpl_vars[\'page\']->_loop = true;
?>	<li><a href="<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/encyclopedia/view/<?php echo $_smarty_tpl->tpl_vars[\'page\']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'page\']->value->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
</a>
	<?php if ($_smarty_tpl->tpl_vars[\'page\']->value->encyclopedia__1){?><?php Smarty_Internal_Function_Call_Handler::call (\'encyclopedia\',$_smarty_tpl,array(\'data\'=>$_smarty_tpl->tpl_vars[\'page\']->value->encyclopedia__1),\'21262246885126d2adedc996_39122634\',false);?>
<?php }?>
	</li>
<?php } ?>
</ol>',
      'nocache_hash' => '21262246885126d2adedc996-39122634',
      'has_nocache_code' => false,
      'called_functions' => 
      array (
        0 => 'encyclopedia',
      ),
    ),
  ),
  'cache_lifetime' => 86400,
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5126d2ae17600',
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
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5126d2ae17600')) {function content_5126d2ae17600($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_bbcode')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.bbcode.php';
if (!is_callable('smarty_modifier_emoticon')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.emoticon.php';
?><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php $_smarty = $_smarty_tpl->smarty; if (!is_callable(\'smarty_modifier_date_format\')) include \'/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php\';
?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: <?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>Zugriff nicht gestattet<?php }?></title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_css/encyclopedia.css" />
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
	<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if (isset($_smarty_tpl->tpl_vars[\'user\']->value)){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<a id="notifypic" href="#"><img src="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'user\']->value->avatar){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
0<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
.jpg" /></a>
		Sali Vuz, <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'user\']->value->name;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>! [<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/log/out">abmelden</a>]
	<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		Sali Vuz, Wanderer! [<a class="link" href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/register">registrieren</a>|<a class="link" href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/log">anmelden</a>]
	<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		</div></div>
		<h2><?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>Zugriff nicht gestattet<?php }?></h2>
		<div id="content">
		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php  $_smarty_tpl->tpl_vars[\'i\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'i\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'notice\']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'i\']->key => $_smarty_tpl->tpl_vars[\'i\']->value){
$_smarty_tpl->tpl_vars[\'i\']->_loop = true;
?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<p class="notice notice_<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'i\']->value[\'type\'];?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars[\'i\']->value[\'notice\']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</p>
		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php } ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		
<h3><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->title, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'permission\']->value->getPermission($_smarty_tpl->tpl_vars[\'obj\']->value,\'createencyclopedia\')>0){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
 <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/encyclopedia/create/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'obj\']->value;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
" class="option create" title="Unterseite erstellen">Unterseite erstellen</a><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ((Permission::getPermission($_smarty_tpl->tpl_vars[\'obj\']->value,\'editencyclopedia\')==2||(Permission::getPermission($_smarty_tpl->tpl_vars[\'obj\']->value,\'editencyclopedia\')==1&&$_smarty_tpl->tpl_vars[\'user\']->value->id==$_smarty_tpl->tpl_vars[\'obj\']->value->user->id))){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
 <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/encyclopedia/edit/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'obj\']->value;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
" class="option edit" title="bearbeiten">bearbeiten</a><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ((Permission::getPermission($_smarty_tpl->tpl_vars[\'obj\']->value,\'deleteencyclopedia\')==2||(Permission::getPermission($_smarty_tpl->tpl_vars[\'obj\']->value,\'deleteencyclopedia\')==1&&$_smarty_tpl->tpl_vars[\'user\']->value->id==$_smarty_tpl->tpl_vars[\'obj\']->value->user->id))){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
 <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/encyclopedia/delete/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'obj\']->value;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
" class="option delete" title="l&ouml;schen">l&ouml;schen</a><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</h3>
<p><?php echo smarty_modifier_emoticon(smarty_modifier_bbcode(nl2br(mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->text, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8')),$_smarty_tpl->tpl_vars['bbcodes']->value),$_smarty_tpl->tpl_vars['emoticons']->value);?>
</p>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->encyclopedia__1){?>
<h3>Unterseiten</h3>
<?php Smarty_Internal_Function_Call_Handler::call ('encyclopedia',$_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value->encyclopedia__1),'21262246885126d2adedc996_39122634',false);?>

<?php }?>

		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a>
<?php /*  Call merged included template "../_function/breadcrumbs.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/breadcrumbs.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, null, array(), 0, '21262246885126d2adedc996-39122634');
content_51a0245113b04($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/breadcrumbs.htm" */?>
&raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia">Kompendium</a><?php if ($_smarty_tpl->tpl_vars['obj']->value->name&&$_smarty_tpl->tpl_vars['obj']->value->encyclopedia!='0'){?><?php Smarty_Internal_Function_Call_Handler::call ('breadcrumbs',$_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value->encyclopedia,'url'=>'encyclopedia/view','parent'=>'encyclopedia'),'21262246885126d2adedc996_39122634',false);?>
<?php }?> &rarr; <?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>Zugriff nicht gestattet<?php }?></p>
			<div id="topoptions"></div>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'online\']->value->data){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			<p id="online">
				Derzeit online: <br />
				<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php  $_smarty_tpl->tpl_vars[\'value\'] = new Smarty_Variable; $_smarty_tpl->tpl_vars[\'value\']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars[\'online\']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, \'array\');}
foreach ($_from as $_smarty_tpl->tpl_vars[\'value\']->key => $_smarty_tpl->tpl_vars[\'value\']->value){
$_smarty_tpl->tpl_vars[\'value\']->_loop = true;
?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

				<span>
					<img src="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/img/user_avatar.id/thumb/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->user->avatar){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
0<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
.jpg" /><a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/user/view/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->user;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->user->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a><br />
					<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars[\'value\']->value->time,$_smarty_tpl->tpl_vars[\'config\']->value->time);?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
:
		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'index\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'std\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
">Index</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'online\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
">Wer ist online?</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Index, Seite <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'board\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
">Foren&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'permissions\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Rechte im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Forum, Seite <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'thread\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'create\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Thema im Forum &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/board/view/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo; erstellen
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'delete\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Thema &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo; l&ouml;schen
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Thema, Seite <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->controller==\'user\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php if ($_smarty_tpl->tpl_vars[\'value\']->value->action==\'viewall\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
">Mitglieder&uuml;bersicht</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'view\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>&laquo;
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }elseif($_smarty_tpl->tpl_vars[\'value\']->value->action==\'edit\'){?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Mitglied &raquo;<a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->location;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[\'value\']->value->location->name, ENT_QUOTES, \'UTF-8\', true), "HTML-ENTITIES", \'UTF-8\');?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a> &laquo; bearbeiten
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Mitglieder, Seite <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }else{ ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
Seite <a href="<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'config\']->value->url;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
"><?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->controller;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
/<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php echo $_smarty_tpl->tpl_vars[\'value\']->value->action;?>
/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>
</a>
		<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

				</span>
				<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php } ?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

			</p>
			<?php echo '/*%%SmartyNocache:21262246885126d2adedc996-39122634%%*/<?php }?>/*/%%SmartyNocache:21262246885126d2adedc996-39122634%%*/';?>

		</div>
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-05-25 04:39:13
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm" */ ?>
<?php if ($_valid && !is_callable('content_51a0245113b04')) {function content_51a0245113b04($_smarty_tpl) {?><?php }} ?>