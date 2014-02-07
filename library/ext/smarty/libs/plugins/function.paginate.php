<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {paginate} function plugin
 *
 * Type:     function<br>
 * Name:     paginate<br>
 * Date:     December 31, 2011
 * Purpose:  output pagination dependent on total pagecount and current page.<br>
 * Params:
 * <pre>
 * - link       - (required) - link where the paginations lead
 * - total      - (required) - total pagecount
 * - cur        - (optional) - current page
 * </pre>
 * Examples:
 * <pre>
 * {paginate link="link.php" total="5"}
 * {paginate link="link.php" total="5" cur="2"}
 * </pre>
 *
 * @version 1.0
 * @author Feal <feals dot de>
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string
 */
function smarty_function_paginate($params, $template) {
	if (empty($params['link'])) {
		trigger_error("paginate: missing 'link' parameter", E_USER_WARNING);
		return;
	}
	elseif (empty($params['total'])) {
		trigger_error("paginate: missing 'total' parameter", E_USER_WARNING);
		return;
	}
	else {
		$link = &$params['link'];
		$total = &$params['total'];
	}
	
	if (empty($params['cur'])) $cur = 0;
	else $cur = &$params['cur'];
	
	$string = array();
	
	for ($i = 1; $i <= $total; $i++) {
		if ($i != $cur) {
			if ($i != 1) $string[] = '<a href="'.$link.'/'.$i.'">'.$i.'</a>';
			else $string[] = '<a href="'.$link.'">'.$i.'</a>';
		}
		else $string[] = '<em>'.$i.'</em>';
	}

	if(count($string) > 1) return '<div class="pagination">'.join(' ', $string).'</div>';
	else return '';
}

?>