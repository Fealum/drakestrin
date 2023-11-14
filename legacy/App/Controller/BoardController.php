<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Configuration;
use Legacy\Library\Class\Source;
use Legacy\App\Model\ConfigurationModel;
use \DateTime;
use \DateInterval;

class BoardController extends Controller
{

	protected $stdaction = 'filter';

	function std($order = 'sort,a;name,a')
	{
		// $this->viewall($order);
		$this->filter();
	}

	function viewall($order = 'sort,a;name,a')
	{
		$this->obj = new _list('board', 'board = 0', array($order, array('sort' => 'sort', 'name' => 'name')));
		if (!is_null($this->user)) $this->set('viewed', $this->getviewed($this->obj->data));
		$this->set('obj', $this->obj);
	}

	function getviewed($array)
	{
		$viewed = array();
		if ($array) foreach ($array as $cur) {
			$threads = new _list('thread', 'board = ' . $cur->id . ' AND post__last_time >= ' . $this->user->lastvisit);
			if ($threads->data) foreach ($threads->data as $i) {
				if ((isset($this->session->viewed[1][$i->id]) && $this->session->viewed[1][$i->id] < $i->post__last_time) || !isset($this->session->viewed[1][$i->id])) {
					$viewed[$cur->id] = TRUE;
					break;
				}
			}
			if ($cur->board__1) $viewed = $viewed + $this->getviewed($cur->board__1);
		}
		return $viewed;
	}

	function changeshow($id = 1, $change = NULL, $ajax = 0)
	{
		if (is_null($this->user)) $this->move('board');
		$board = Cache::_($this->_model, $id);
		$configurations = new _list('configuration', 'table__recipient = 0 AND recipient = ' . $this->user->id . ' AND table__subject = 3 AND subject = ' . $board->id . ' AND setting = 4');
		if (is_null($change)) $change = (Configuration::getConfiguration($board, 'show') == '0') ? '1' : '0';
		if (isset($configurations->data[0]))
			$configurations->data[0]->update(array('value' => $change));
		else
			new ConfigurationModel(NULL, array('table__recipient' => '0', 'recipient' => $this->user->id, 'table__subject' => '3', 'subject' => $board->id, 'setting' => 4, 'value' => $change));
		if ($ajax == 0) $this->move('board');
		$this->set('change', $change);
	}

	function setfilter()
	{
		$filter = array('title' => $this->post('title'), 'message' => $this->post('message'), 'board' => $this->post('board'), 'user_first' => $this->post('user_first'), 'user_contains' => $this->post('user_contains'), 'user_last' => $this->post('user_last'));
		$filterpath = null;
		if (isset($filter['title']) && $filter['title'] != '') {
			$filterpath[] = 'title:' . urlencode($filter['title']);
		}
		if (isset($filter['message']) && $filter['message'] != '') {
			$filterpath[] = 'message:' . urlencode($filter['message']);
		}
		if (isset($filter['board'])) {
			foreach ($filter['board'] as $key => $value) {
				$filter['board'][$key] = (int)$value;
			}
			$filterpath[] = 'board:' . join(',', $filter['board']);
		}
		if (isset($filter['user_first']) && $filter['user_first'] != '') {
			$filter['user_first'] = explode(',', $filter['user_first']);
			foreach ($filter['user_first'] as $key => $value) {
				$filter['user_first'][$key] = (int)$value;
			}
			$filterpath[] = 'user_first:' . join(',', $filter['user_first']);
		}
		if (isset($filter['user_contains']) && $filter['user_contains'] != '') {
			$filter['user_contains'] = explode(',', $filter['user_contains']);
			foreach ($filter['user_contains'] as $key => $value) {
				$filter['user_contains'][$key] = (int)$value;
			}
			$filterpath[] = 'user_contains:' . join(',', $filter['user_contains']);
		}
		if (isset($filter['user_last']) && $filter['user_last'] != '') {
			$filter['user_last'] = explode(',', $filter['user_last']);
			foreach ($filter['user_last'] as $key => $value) {
				$filter['user_last'][$key] = (int)$value;
			}
			$filterpath[] = 'user_last:' . join(',', $filter['user_last']);
		}
		if ($filterpath != null) $this->move('board/filter/' . join(';', $filterpath));
		else $this->move('board');
	}

	function filter($filter = null, $page = 1)
	{
		$boards = new _list('board', 'board = 0', array('sort,a;name,a', array('sort' => 'sort', 'name' => 'name')));
		$this->set('boards', $boards);

		$this->set('filterstring', '');
		if (is_numeric($filter)) {
			$page = $filter;
			$filter = null;
		}
		$this->set('page', $page);
		$filterarr = null;
		$userfilter = null;
		$charfilter = null;
		$postfiltering = false;
		if ($filter != null && $filter != '') {
			$filterarr = explode(';', $filter);
			foreach ($filterarr as $key => $value) {
				$value = explode(':', $value);
				unset($filterarr[$key]);
				if (count($value) == 2) $filterarr[$value[0]] = $value[1];
			}
			if (isset($filterarr['title']) && $filterarr['title'] != '') {
				$filterbits[] = "LOWER(`name`) LIKE LOWER('%" . Source::getInstance('mysql1')->escape($filterarr['title']) . "%')";
			}
			if (isset($filterarr['message']) && $filterarr['message'] != '') {
				$postfiltering = true;
				$filterbits[] = "LOWER(`message`) LIKE LOWER('%" . Source::getInstance('mysql1')->escape($filterarr['message']) . "%')";
			}
			if (isset($filterarr['board'])) {
				$filterarr['board'] = explode(",", $filterarr['board']);
				foreach ($filterarr['board'] as $key => $val) {
					$val = (int) $val;
					$boardpiece[$val] = $val;
				}
				$filterarr['board'] = $boardpiece;
				$filterbits[] = "`board` IN (" . join(', ', $filterarr['board']) . ")";
			}
			if (isset($filterarr['user_first'])) {
				$postfiltering = true;
				$filterarr['user_first'] = explode(',', $filterarr['user_first']);
				foreach ($filterarr['user_first'] as $key => $val) {
					$val = (int) $val;
					$userfilter['f'][] = Cache::_('UserModel', $val);
					$userfpiece[$val] = $val;
				}
				$filterarr['user_first'] = $userfpiece;
				$filterbits[] = "`post__first_user` IN (" . join(', ', $filterarr['user_first']) . ")";
			}
			if (isset($filterarr['user_contains'])) {
				$postfiltering = true;
				$filterarr['user_contains'] = explode(',', $filterarr['user_contains']);
				foreach ($filterarr['user_contains'] as $key => $val) {
					$val = (int) $val;
					$userfilter['c'][] = Cache::_('UserModel', $val);
					$usercpiece[$val] = $val;
				}
				$filterarr['user_contains'] = $usercpiece;
				$filterbits[] = "`user` IN (" . join(', ', $filterarr['user_contains']) . ")";
			}
			if (isset($filterarr['user_last'])) {
				$postfiltering = true;
				$filterarr['user_last'] = explode(',', $filterarr['user_last']);
				foreach ($filterarr['user_last'] as $key => $val) {
					$val = (int) $val;
					$userfilter['l'][] = Cache::_('UserModel', $val);
					$userlpiece[$val] = $val;
				}
				$filterarr['user_last'] = $userlpiece;
				$filterbits[] = "`post__last_user` IN (" . join(', ', $filterarr['user_last']) . ")";
			}
			if (isset($filterarr['char_first'])) {
				$postfiltering = true;
				$filterarr['char_first'] = explode(',', $filterarr['char_first']);
				foreach ($filterarr['char_first'] as $key => $val) {
					$val = (int) $val;
					$charfilter['f'][] = Cache::_('CharacterModel', $val);
					$charfpiece[$val] = $val;
				}
				$filterarr['char_first'] = $charfpiece;
				$filterbits[] = "`post__first_character` IN (" . join(', ', $filterarr['char_first']) . ")";
			}
			if (isset($filterarr['char_contains'])) {
				$postfiltering = true;
				$filterarr['char_contains'] = explode(',', $filterarr['char_contains']);
				foreach ($filterarr['char_contains'] as $key => $val) {
					$val = (int) $val;
					$charfilter['c'][] = Cache::_('CharacterModel', $val);
					$charcpiece[$val] = $val;
				}
				$filterarr['char_contains'] = $charcpiece;
				$filterbits[] = "`character` IN (" . join(', ', $filterarr['char_contains']) . ")";
			}
			if (isset($filterarr['char_last'])) {
				$postfiltering = true;
				$filterarr['char_last'] = explode(',', $filterarr['char_last']);
				foreach ($filterarr['char_last'] as $key => $val) {
					$val = (int) $val;
					$charfilter['l'][] = Cache::_('CharacterModel', $val);
					$charlpiece[$val] = $val;
				}
				$filterarr['char_last'] = $charlpiece;
				$filterbits[] = "`post__last_character` IN (" . join(', ', $filterarr['char_last']) . ")";
			}
			if (isset($filterarr['date_first']) && $filterarr['date_first'] != '') {
				if (substr($filterarr['date_first'], -1) == 'd') {
					$days = substr($filterarr['date_first'], 0, -1);
					$filterarr['date_first'] = new DateTime();
					$filterarr['date_first'] = $filterarr['date_first']->sub(new DateInterval('P' . $days . 'D'));
					if ($filterarr['date_first'] != FALSE)
						$filterbits[] = "`post__last_time` >= " . $filterarr['date_first']->format('U');
				} else {
					$filterarr['date_first'] = explode('-', $filterarr['date_first']);
					$filterarr['date_first'][0] = DateTime::createFromFormat('d.m.Y', $filterarr['date_first'][0]);
					$filterarr['date_first'][1] = DateTime::createFromFormat('d.m.Y', $filterarr['date_first'][1]);
					if ($filterarr['date_first'][0] != FALSE)
						$filterarr['date_first'][0] = "`post__first_time` >= " . $filterarr['date_first'][0]->format('U');
					else
						unset($filterarr['date_first'][0]);
					if ($filterarr['date_first'][1] != FALSE)
						$filterarr['date_first'][1] = "`post__first_time` <= " . $filterarr['date_first'][1]->format('U');
					else
						unset($filterarr['date_first'][1]);
					if (isset($filterarr['date_first'][0]) || isset($filterarr['date_first'][1]))
						$filterbits[] = "(" . join(' AND ', $filterarr['date_first']) . ")";
				}
			}
			if (isset($filterarr['date_last']) && $filterarr['date_last'] != '') {
				if (substr($filterarr['date_last'], -1) == 'd') {
					$days = substr($filterarr['date_last'], 0, -1);
					$filterarr['date_last'] = new DateTime();
					$filterarr['date_last'] = $filterarr['date_last']->sub(new DateInterval('P' . $days . 'D'));
					if ($filterarr['date_last'] != FALSE)
						$filterbits[] = "`post__last_time` >= " . $filterarr['date_last']->format('U');
				} else {
					$filterarr['date_last'] = explode('-', $filterarr['date_last']);
					$filterarr['date_last'][0] = DateTime::createFromFormat('d.m.Y', $filterarr['date_last'][0]);
					$filterarr['date_last'][1] = DateTime::createFromFormat('d.m.Y', $filterarr['date_last'][1]);
					if ($filterarr['date_last'][0] != FALSE)
						$filterarr['date_last'][0] = "`post__last_time` >= " . $filterarr['date_last'][0]->format('U');
					else
						unset($filterarr['date_last'][0]);
					if ($filterarr['date_last'][1] != FALSE)
						$filterarr['date_last'][1] = "`post__last_time` <= " . $filterarr['date_last'][1]->format('U');
					else
						unset($filterarr['date_last'][1]);
					if (isset($filterarr['date_last'][0]) || isset($filterarr['date_last'][1]))
						$filterbits[] = "(" . join(' AND ', $filterarr['date_last']) . ")";
				}
			}
			$this->set('filterstring', '/' . $filter);
			$filter = isset($filterbits) ? join(' AND ', $filterbits) : null;
		}
		$this->set('filter', $filterarr);
		$this->set('userfilter', $userfilter);
		$this->set('charfilter', $charfilter);
		if ($postfiltering == false)
			$this->obj = new _list('thread', $filter, array('post__last_time,d', array('post__last_time' => 'post__last_time')), array('', ''), true);
		else
			$this->obj = new _list('threadfilter', $filter, array('post__last_time,d', array('post__last_time' => 'post__last_time')), array('', ''), true, 'post__first', 'post__first');
		$this->set('viewed', $this->session->viewed);
		$this->set('obj', $this->obj);
	}

	function view($id = 1, $page = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(3, $this->obj->id);
		$this->set('page', $page);
		if (!is_null($this->user)) $this->set('viewedboards', $this->getviewed($this->obj->board__1));
		$this->set('viewed', $this->session->viewed);
		$this->set('obj', $this->obj);
	}

	function permissions($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->onlinelocation(3, $this->obj->id);
		$this->set('obj', $this->obj);
	}

	function ajax__getusers()
	{
		$query = addslashes($this->post('q'));
		$this->obj = new _list('user', '`name` LIKE "%' . $query . '%" OR id = "' . $query . '"', array('wordlength,a', array('wordlength' => 'LENGTH(`name`)')), array(10, null));
		$this->set('query', $this->post('q'));
		$this->set('obj', $this->obj);
	}

	function ajax__getchars()
	{
		$query = addslashes($this->post('q'));
		$this->obj = new _list('character', '`name` LIKE "%' . $query . '%" OR id = "' . $query . '"', array('wordlength,a', array('wordlength' => 'LENGTH(`name`)')), array(10, null));
		$this->set('query', $this->post('q'));
		$this->set('obj', $this->obj);
	}
}
