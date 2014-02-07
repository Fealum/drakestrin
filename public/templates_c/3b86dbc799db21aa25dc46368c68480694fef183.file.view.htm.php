<?php /* Smarty version Smarty-3.1.5, created on 2013-06-04 14:22:28
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/board/view.htm" */ ?>
<?php /*%%SmartyHeaderCode:5153055955126c733d94422-61984305%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b86dbc799db21aa25dc46368c68480694fef183' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/board/view.htm',
      1 => 1370348441,
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
    '4654246694f344a3387a59dcaf528cd2a4b9bf29' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/pagination.htm',
      1 => 1370348447,
      2 => 'file',
    ),
    '88319fd988ec356ba55fe6bcb8756d4033a7cdb0' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm',
      1 => 1366133164,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5153055955126c733d94422-61984305',
  'function' => 
  array (
    'breadcrumbs' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'boards' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'datetime' => 
    array (
      'parameter' => 
      array (
        'objvalue' => NULL,
      ),
      'compiled' => '',
    ),
    'pagination' => 
    array (
      'parameter' => 
      array (
        'borders' => 3,
      ),
      'compiled' => '',
    ),
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_5126c7340d181',
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
<?php if ($_valid && !is_callable('content_5126c7340d181')) {function content_5126c7340d181($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
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
/templates/standard/_css/board_id.css" />
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
		<h2><?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>Zugriff nicht gestattet<?php }?></h2>
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
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/datetime.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '5153055955126c733d94422-61984305');
content_51addc046b16f($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/datetime.htm" */?>
<?php /*  Call merged included template "../_function/pagination.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/pagination.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '5153055955126c733d94422-61984305');
content_51addc0473c13($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/pagination.htm" */?>
<?php if (!function_exists('smarty_template_function_boards')) {
    function smarty_template_function_boards($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['boards']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
<ol>
<?php  $_smarty_tpl->tpl_vars['board'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['board']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['board']->key => $_smarty_tpl->tpl_vars['board']->value){
$_smarty_tpl->tpl_vars['board']->_loop = true;
?>	<li>
		<h4><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board/view/<?php echo $_smarty_tpl->tpl_vars['board']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['board']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a><?php if (isset($_smarty_tpl->tpl_vars['viewed']->value[$_smarty_tpl->tpl_vars['board']->value->id])){?> <span class="option new">(Neu)</span><?php }?><?php if ($_smarty_tpl->tpl_vars['board']->value->board__1){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board/changeshow/<?php echo $_smarty_tpl->tpl_vars['board']->value;?>
" class="option <?php if ($_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['board']->value,'show')!='0'){?>contract">verk&uuml;rzen<?php }else{ ?>expand">erweitern<?php }?></a><?php }?></h4>
<?php if (!$_smarty_tpl->tpl_vars['board']->value->cat){?>
			<p class="small">
				<?php if ($_smarty_tpl->tpl_vars['board']->value->post__last){?><span><img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/user_avatar.id/thumb/<?php if ($_smarty_tpl->tpl_vars['board']->value->post__last->user->avatar){?><?php echo $_smarty_tpl->tpl_vars['board']->value->post__last->user;?>
<?php }else{ ?>0<?php }?>.jpg" /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/thread/view/<?php echo $_smarty_tpl->tpl_vars['board']->value->post__last->thread;?>
/last#post<?php echo $_smarty_tpl->tpl_vars['board']->value->post__last;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['board']->value->post__last->thread->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a><br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['board']->value->post__last->user;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['board']->value->post__last->user->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>; <?php smarty_template_function_datetime($_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['board']->value->post__last->time));?>
</span><?php }?>
				<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['board']->value->description, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<br /><em><?php echo $_smarty_tpl->tpl_vars['board']->value->thread__total;?>
 Themen, <?php echo $_smarty_tpl->tpl_vars['board']->value->post__total;?>
 Beitr&auml;ge</em>
			</p>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['board']->value->board__1&&$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['board']->value,'show')!='0'){?><?php smarty_template_function_boards($_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['board']->value->board__1));?>
<?php }?>
	</li>
<?php } ?>
</ol><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>

<?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><p><?php if ($_smarty_tpl->tpl_vars['obj']->value->board__1){?><?php echo count($_smarty_tpl->tpl_vars['obj']->value->board__1);?>
 Unterforen<?php if (!$_smarty_tpl->tpl_vars['obj']->value->cat){?>, <?php }?><?php }?><?php if (!$_smarty_tpl->tpl_vars['obj']->value->cat){?><?php echo $_smarty_tpl->tpl_vars['obj']->value->thread__total;?>
 Themen, <?php echo $_smarty_tpl->tpl_vars['obj']->value->post__total;?>
 Beitr&auml;ge<?php }?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->board__1){?>
<h3>Unterforen</h3>
<div id="boards">
<?php smarty_template_function_boards($_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value->board__1));?>

</div>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['obj']->value->thread){?>
<h3>Themen<?php if ($_smarty_tpl->tpl_vars['permission']->value->getPermission($_smarty_tpl->tpl_vars['obj']->value,'createthread')>0){?> <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/thread/create/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" class="option create" title="neues Thema erstellen">neues Thema erstellen</a><?php }?></h3>
<div id="threads">
<?php smarty_template_function_pagination($_smarty_tpl,array('link'=>"/board/view/".($_smarty_tpl->tpl_vars['obj']->value),'total'=>ceil((int)count($_smarty_tpl->tpl_vars['obj']->value->thread)/$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries')),'cur'=>$_smarty_tpl->tpl_vars['page']->value));?>

<ol>
<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['obj']->value->thread; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['value']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
 $_smarty_tpl->tpl_vars['value']->iteration++;
?>
<?php if ($_smarty_tpl->tpl_vars['value']->iteration>(($_smarty_tpl->tpl_vars['page']->value-1)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))&&$_smarty_tpl->tpl_vars['value']->iteration<=(($_smarty_tpl->tpl_vars['page']->value)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))){?>
	<li>
		<h4><?php if ($_smarty_tpl->tpl_vars['value']->value->important){?>[Wichtig] <?php }?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/thread/view/<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a><?php if (isset($_smarty_tpl->tpl_vars['user']->value)&&$_smarty_tpl->tpl_vars['value']->value->post__last_time>=$_smarty_tpl->tpl_vars['user']->value->lastvisit&&((isset($_smarty_tpl->tpl_vars['viewed']->value[1][$_smarty_tpl->tpl_vars['value']->value->id])&&$_smarty_tpl->tpl_vars['viewed']->value[1][$_smarty_tpl->tpl_vars['value']->value->id]<$_smarty_tpl->tpl_vars['value']->value->post__last_time)||!isset($_smarty_tpl->tpl_vars['viewed']->value[1][$_smarty_tpl->tpl_vars['value']->value->id]))){?> <span class="option new">(Neu)</span><?php }?></h4>
		<p class="small"><?php echo $_smarty_tpl->tpl_vars['value']->value->views;?>
 Aufrufe und <?php if ($_smarty_tpl->tpl_vars['value']->value->post__total==1){?>ein Beitrag<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['value']->value->post__total;?>
 Beitr&auml;ge<?php }?></p>
		<p class="small"><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['value']->value->post[0]->user;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->post[0]->user->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>; <?php smarty_template_function_datetime($_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['value']->value->post[0]->time));?>
<br />
		&mdash;<a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['value']->value->post__last->user;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->post__last->user->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a>; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/thread/view/<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
/last#post<?php echo $_smarty_tpl->tpl_vars['value']->value->post__last;?>
"><?php smarty_template_function_datetime($_smarty_tpl,array('value'=>$_smarty_tpl->tpl_vars['value']->value->post__last_time));?>
</a></p>
	</li>
<?php }elseif($_smarty_tpl->tpl_vars['value']->iteration>(($_smarty_tpl->tpl_vars['page']->value)*$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries'))){?><?php break 1?><?php }?>
<?php } ?>
</ol>
<?php smarty_template_function_pagination($_smarty_tpl,array('link'=>"/board/view/".($_smarty_tpl->tpl_vars['obj']->value),'total'=>ceil((int)count($_smarty_tpl->tpl_vars['obj']->value->thread)/$_smarty_tpl->tpl_vars['configuration']->value->getConfiguration($_smarty_tpl->tpl_vars['obj']->value,'pageentries')),'cur'=>$_smarty_tpl->tpl_vars['page']->value));?>

</div>
<?php }?>
<?php }else{ ?>
<h3>Zugriff nicht gestattet!</h3>
<p>Leider ist Dir der Zugriff auf dieses Forum nicht gestattet. Dir fehlen die daf&uuml;r erforderlichen Rechte. Wenn Du glaubst, dass es sich dabei um einen Fehler handelt, benachrichtige bitte den Administrator.</p>
<?php }?>

		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a>
<?php /*  Call merged included template "../_function/breadcrumbs.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/breadcrumbs.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '5153055955126c733d94422-61984305');
content_51addc0496326($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/breadcrumbs.htm" */?>
&raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board">Forum</a><?php if ($_smarty_tpl->tpl_vars['obj']->value->name&&$_smarty_tpl->tpl_vars['obj']->value->board!='0'){?><?php smarty_template_function_breadcrumbs($_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value->board,'url'=>'board/view','parent'=>'board'));?>
<?php }?> &rarr; <?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>Zugriff nicht gestattet<?php }?></p>
			<div id="topoptions"><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/board/permissions/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
">Rechte</a> <a href="#">Journal</a></div>
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
</html><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-06-04 14:22:28
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/datetime.htm" */ ?>
<?php if ($_valid && !is_callable('content_51addc046b16f')) {function content_51addc046b16f($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/var/www/l3s658/html/drakestrin/library/ext/smarty/libs/plugins/modifier.date_format.php';
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
<?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-06-04 14:22:28
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/pagination.htm" */ ?>
<?php if ($_valid && !is_callable('content_51addc0473c13')) {function content_51addc0473c13($_smarty_tpl) {?><?php if (!function_exists('smarty_template_function_pagination')) {
    function smarty_template_function_pagination($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['pagination']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
<?php if ($_smarty_tpl->tpl_vars['total']->value>1){?>
<div class="pagination">
<?php $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int)ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? $_smarty_tpl->tpl_vars['total']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['total']->value)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0){
for ($_smarty_tpl->tpl_vars['i']->value = 1, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++){
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration == 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration == $_smarty_tpl->tpl_vars['i']->total;?>
<?php if ($_smarty_tpl->tpl_vars['i']->value<=($_smarty_tpl->tpl_vars['borders']->value)||($_smarty_tpl->tpl_vars['i']->value>$_smarty_tpl->tpl_vars['cur']->value-$_smarty_tpl->tpl_vars['borders']->value&&$_smarty_tpl->tpl_vars['i']->value<$_smarty_tpl->tpl_vars['cur']->value+$_smarty_tpl->tpl_vars['borders']->value)||$_smarty_tpl->tpl_vars['i']->value>($_smarty_tpl->tpl_vars['total']->value-$_smarty_tpl->tpl_vars['borders']->value)){?><?php if ($_smarty_tpl->tpl_vars['i']->value!=$_smarty_tpl->tpl_vars['cur']->value){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['link']->value;?>
<?php if ($_smarty_tpl->tpl_vars['i']->value>1){?>/<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
<?php }?>"><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</a><?php }else{ ?><em><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</em><?php }?><?php }elseif(($_smarty_tpl->tpl_vars['i']->value>$_smarty_tpl->tpl_vars['borders']->value&&$_smarty_tpl->tpl_vars['i']->value==$_smarty_tpl->tpl_vars['cur']->value-$_smarty_tpl->tpl_vars['borders']->value)||($_smarty_tpl->tpl_vars['i']->value==$_smarty_tpl->tpl_vars['cur']->value+$_smarty_tpl->tpl_vars['borders']->value&&$_smarty_tpl->tpl_vars['i']->value<$_smarty_tpl->tpl_vars['total']->value-$_smarty_tpl->tpl_vars['borders']->value)){?>&hellip;<?php }?> 
<?php }} ?>
</div>
<?php }?><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-06-04 14:22:28
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm" */ ?>
<?php if ($_valid && !is_callable('content_51addc0496326')) {function content_51addc0496326($_smarty_tpl) {?><?php if (!function_exists('smarty_template_function_breadcrumbs')) {
    function smarty_template_function_breadcrumbs($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['breadcrumbs']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
<?php if ($_smarty_tpl->tpl_vars['data']->value->{$_smarty_tpl->tpl_vars['parent']->value}!='0'){?><?php smarty_template_function_breadcrumbs($_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['data']->value->{$_smarty_tpl->tpl_vars['parent']->value},'url'=>$_smarty_tpl->tpl_vars['url']->value,'parent'=>$_smarty_tpl->tpl_vars['parent']->value));?>
<?php }?> &rarr; <?php if ($_smarty_tpl->tpl_vars['data']->value->name){?><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['data']->value;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['data']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }else{ ?>&hellip;<?php }?></a><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php }} ?>