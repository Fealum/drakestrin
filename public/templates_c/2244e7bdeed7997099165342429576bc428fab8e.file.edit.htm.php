<?php /* Smarty version Smarty-3.1.5, created on 2013-05-20 20:23:23
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/edit.htm" */ ?>
<?php /*%%SmartyHeaderCode:1895393394516b129be888d0-16357234%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2244e7bdeed7997099165342429576bc428fab8e' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/encyclopedia/edit.htm',
      1 => 1361493158,
      2 => 'file',
    ),
    'e757fb0429ab7f4d3c82a82accfeb5318b5625be' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_.htm',
      1 => 1366402498,
      2 => 'file',
    ),
    'fc36e469cd1fc0fbcaebfd44df71bbee3d1a46a3' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/textinput.htm',
      1 => 1366133164,
      2 => 'file',
    ),
    '88319fd988ec356ba55fe6bcb8756d4033a7cdb0' => 
    array (
      0 => '/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm',
      1 => 1366133164,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1895393394516b129be888d0-16357234',
  'function' => 
  array (
    'breadcrumbs' => 
    array (
      'parameter' => 
      array (
      ),
      'compiled' => '',
    ),
    'textinput' => 
    array (
      'parameter' => 
      array (
        'type' => 'text',
        'maxlength' => false,
        'autofocus' => false,
        'required' => true,
      ),
      'compiled' => '',
    ),
  ),
  'version' => 'Smarty-3.1.5',
  'unifunc' => 'content_516b129c2f70b',
  'variables' => 
  array (
    'config' => 0,
    'user' => 1,
    'notice' => 0,
    'i' => 0,
    'online' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_516b129c2f70b')) {function content_516b129c2f70b($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
: <?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?>Seite &raquo;<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&laquo; editieren<?php }else{ ?>Zugriff nicht gestattet<?php }?></title>
<meta http-equiv="language" content="deutsch, de" />
<meta name="keywords" content="Drachenstein, Kaiserreich, Mikronation, Virtuelle Nation, Forenrollenspiel, Forumrollenspiel, Rollenspiel, Browserspiel, Browser-Rollenspiel, Mittelalter, Fantasy, Drache, kostenlos, Kaiser, Veuxin, Hobbit, Vampir" />
<meta name="Description" content="Das Kaiserreich Drachenstein ist eine sogenannte Micronation, ein Browser-basiertes Rollenspiel, in dem ein virtueller mittelalterlicher Fantasy-Staat simuliert wird. " />
<meta name="Author" content="Fabian Müller" />
<meta name="copyright" content="Fabian Müller" />
<meta name="page-topic" content="Unterhaltung" />
<meta name="revisit-after" content="14 days" />
<meta http-equiv="imagetoolbar" content="false" />
<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/templates/standard/_css/_.css" />
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
		<h2><?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?>Seite &raquo;<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&laquo; editieren<?php }else{ ?>Zugriff nicht gestattet<?php }?></h2>
		<div id="content">
		<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['notice']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value){
$_smarty_tpl->tpl_vars['i']->_loop = true;
?>
		<p class="notice notice_<?php echo $_smarty_tpl->tpl_vars['i']->value['type'];?>
"><?php echo $_smarty_tpl->getSubTemplate ("../_notice/".($_smarty_tpl->tpl_vars['i']->value['notice']).".htm", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</p>
		<?php } ?>
		
<?php /*  Call merged included template "../_function/textinput.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/textinput.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '1895393394516b129be888d0-16357234');
content_519a6a1baaa12($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/textinput.htm" */?>
<form name="editencyclopedia" action="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia/edit/<?php echo $_smarty_tpl->tpl_vars['obj']->value;?>
" method="post">
<?php smarty_template_function_textinput($_smarty_tpl,array('formname'=>'editencyclopedia','inputname'=>'name','displayname'=>'Seitentitel (kurz)','value'=>$_smarty_tpl->tpl_vars['obj']->value->name));?>

<?php smarty_template_function_textinput($_smarty_tpl,array('formname'=>'editencyclopedia','inputname'=>'title','displayname'=>'Seitentitel (lang)','value'=>$_smarty_tpl->tpl_vars['obj']->value->title));?>

<textarea name="text"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->text, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</textarea>
<p><input type="submit" value="Seite bearbeiten" name="submit" /></p>
</form>

		</div>
		<div id="sidebar">
			<p><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/"><?php echo $_smarty_tpl->tpl_vars['config']->value->title;?>
</a>
<?php /*  Call merged included template "../_function/breadcrumbs.htm" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('../_function/breadcrumbs.htm', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0, '1895393394516b129be888d0-16357234');
content_519a6a1bb75ce($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); /*  End of included template "../_function/breadcrumbs.htm" */?>
&raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/encyclopedia">Kompendium</a><?php if ($_smarty_tpl->tpl_vars['obj']->value->name&&$_smarty_tpl->tpl_vars['obj']->value->encyclopedia!='0'){?><?php smarty_template_function_breadcrumbs($_smarty_tpl,array('data'=>$_smarty_tpl->tpl_vars['obj']->value,'url'=>'encyclopedia/view','parent'=>'encyclopedia'));?>
<?php }?> &rarr; <?php if ($_smarty_tpl->tpl_vars['obj']->value->name){?>Seite &raquo;<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['obj']->value->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&laquo; editieren<?php }else{ ?>Zugriff nicht gestattet<?php }?></p>
			<div id="topoptions"></div>
			<p id="online">
			Derzeit online: <br />
			<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['online']->value->data; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value){
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
			<img src="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/img/user_avatar.id/thumb/<?php if ($_smarty_tpl->tpl_vars['value']->value->user->avatar){?><?php echo $_smarty_tpl->tpl_vars['value']->value->user;?>
<?php }else{ ?>0<?php }?>.jpg" /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/user/view/<?php echo $_smarty_tpl->tpl_vars['value']->value->user;?>
"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value->user->name, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</a><br /> 
			<?php }
if (!$_smarty_tpl->tpl_vars['value']->_loop) {
?>
			Zurzeit ist niemand online.
			<?php } ?>
			</p>
		</div>
		<p class="disclaimer">Morikles 1.0 &copy; Fabian M&uuml;ller, 2005&mdash;2013.<br /><a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/help">Hilfe</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/terms">Nutzungsbedingungen</a>, <a href="<?php echo $_smarty_tpl->tpl_vars['config']->value->url;?>
/static/legal">Impressum</a>.</p>
	</div>
</body>
</html><?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-05-20 20:23:23
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/textinput.htm" */ ?>
<?php if ($_valid && !is_callable('content_519a6a1baaa12')) {function content_519a6a1baaa12($_smarty_tpl) {?><?php if (!function_exists('smarty_template_function_textinput')) {
    function smarty_template_function_textinput($_smarty_tpl,$params) {
    $saved_tpl_vars = $_smarty_tpl->tpl_vars;
    foreach ($_smarty_tpl->smarty->template_functions['textinput']['parameter'] as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);};
    foreach ($params as $key => $value) {$_smarty_tpl->tpl_vars[$key] = new Smarty_variable($value);}?>
<p><label for="<?php echo $_smarty_tpl->tpl_vars['inputname']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['displayname']->value;?>
: </label><input type="<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['inputname']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['inputname']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['maxlength']->value){?> maxlength="<?php echo $_smarty_tpl->tpl_vars['maxlength']->value;?>
"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['value']->value)){?> value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['value']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"<?php }elseif(isset($_smarty_tpl->tpl_vars[(($_smarty_tpl->tpl_vars['formname']->value).($_smarty_tpl->tpl_vars['inputname']->value))]->value)){?> value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars[(($_smarty_tpl->tpl_vars['formname']->value).($_smarty_tpl->tpl_vars['inputname']->value))]->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['placeholder']->value)){?> placeholder="<?php echo $_smarty_tpl->tpl_vars['placeholder']->value;?>
"<?php }?><?php if ($_smarty_tpl->tpl_vars['autofocus']->value){?> autofocus<?php }?><?php if ($_smarty_tpl->tpl_vars['required']->value){?> required<?php }?> /></p><?php $_smarty_tpl->tpl_vars = $saved_tpl_vars;}}?>
<?php }} ?><?php /* Smarty version Smarty-3.1.5, created on 2013-05-20 20:23:23
         compiled from "/var/www/l3s658/html/drakestrin/application/view/standard/_function/breadcrumbs.htm" */ ?>
<?php if ($_valid && !is_callable('content_519a6a1bb75ce')) {function content_519a6a1bb75ce($_smarty_tpl) {?><?php if (!function_exists('smarty_template_function_breadcrumbs')) {
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