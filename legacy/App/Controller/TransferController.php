<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Permission;
use Legacy\Library\Class\Items;
use Legacy\App\Model\InventoryModel;
use Legacy\App\Model\PostModel;
use Legacy\App\Model\TransferModel;
use Legacy\App\Model\TransferitemModel;

class TransferController extends Controller
{

	protected $stdaction = 'view';

	function std()
	{
		$this->view(1, 1);
	}

	private function transfer2($object, $from, $to)
	{
		global $tables;
		if (is_int($from)) $from = Cache::_('CharacterModel', $from);
		if (is_int($to)) $to = Cache::_('CharacterModel', $to);
		if (!$from->id) return FALSE;
		if (!$to->id) return FALSE;
		if (is_array($object[0])) {
			$return = array();
			foreach ($object as $i) {
				$thistransfer = $this->transfer2($i, $from, $to);
				if ($thistransfer != FALSE) $return[] = $thistransfer;
			}
			return (count($return) > 0) ? $return : FALSE;
		}
		if (is_numeric($object[0]))
			$object[0] = Cache::_('InventoryModel', $object[0]);
		if ($object[0]->owner != $from) return FALSE;
		if (!$object[0]->item->stackable) {
			$object[0]->update(array('table__owner' => array_search($to->table, $tables), 'owner' => $to->id));
			return array($object[0]->item, 0);
		}
		$object[1] = (!isset($object[1])) ? 1 : $object[0]->undounitary($object[1]);
		if ($object[1] <= 0)
			$object[1] = 1;
		elseif ($object[1] > $object[0]->stack)
			$object[1] = $object[0]->stack;
		$invatrecipient = new _list('inventory', 'item = ' . $object[0]->item->id . ' AND table__owner = 6' . array_search($to->table, $tables) . ' AND owner = ' . $to->id);
		if ($invatrecipient != NULL) $invatrecipient = $invatrecipient->data[0];
		if ($object[1] == $object[0]->stack && $invatrecipient == NULL) {
			$object[0]->update(array('table__owner' => array_search($to->table, $tables), 'owner' => $to->id));
			return array($object[0]->item, $object[1]);
		} elseif ($object[1] == $object[0]->stack && $invatrecipient != NULL) {
			$invatrecipient->update(array('stack' => $invatrecipient->stack + $object[1]));
			$item = $object[0]->item;
			$object[0]->delete();
			return array($item, $object[1]);
		} elseif ($object[1] < $object[0]->stack && $invatrecipient == NULL) {
			if ($invatrecipient == NULL)
				$newinv = new InventoryModel(NULL, array('item' => $object[0]->item->id, 'stack' => $object[1], 'wear' => 0, 'table__owner' => array_search($to->table, $tables), 'owner' => $to->id));
			else
				$invatrecipient->update(array('stack' => $invatrecipient->stack + $object[1]));
			$object[0]->update(array('stack' => $object[0]->stack - $object[1]));
			return array($object[0]->item, $object[1]);
		}
	}

	function transfer($id = 1)
	{
		$this->obj = Cache::_('ThreadModel', $id);
		$this->set('obj', $this->obj);
		$post = array('thread' => $this->obj->id, 'user' => 2, 'character' => 3, 'time' => time(), 'message' => '', 'smilies' => 0, 'signature' => 0, 'ip' => $_SERVER['REMOTE_ADDR']);
		$transfer = array('inventory' => $this->post('inventory'), 'inventorystack' => $this->post('inventorystack'), 'recipient' => $this->post('recipient'));
		$character = Cache::_('CharacterModel', $this->post('character'));
		$recipient = Cache::_('CharacterModel', $transfer['recipient']);
		if (Permission::getPermission($this->obj, 'transfer') == 0 || !$transfer['inventory'] || $character->user->id != $this->user->id || !$recipient->id || $recipient->id == $character->id) {
			$this->setnotice('transfer_transfer_nopermission', 'error');
			$this->move('thread/view/' . $this->obj->id);
			exit;
		}
		$transferarray = array();
		foreach ($transfer['inventory'] as $k => $i)
			$transferarray[] = array($transfer['inventory'][$k], $transfer['inventorystack'][$k]);
		$transfer = Items::transfer($transferarray, $character, $recipient);
		if (!is_array($transfer)) {
			$this->setnotice('transfer_transfer_nopermission', 'error');
			$this->move('thread/view/' . $this->obj->id . '/last#post' . $newpost->id);
			exit;
		}
		$newpost = new PostModel(NULL, $post);
		$actionchar = Cache::_('CharacterModel', 3);
		$this->obj->update(array('post__total' => ($this->obj->post__total + 1), 'post__last' => $newpost->id, 'post__last_time' => $newpost->time));
		$actionchar->user->update(array('post__total' => ($actionchar->user->post__total + 1)));
		$actionchar->update(array('post__total' => ($actionchar->post__total + 1)));
		$this->obj->board->update(array('post__total' => ($this->obj->board->post__total + 1), 'post__last' => $newpost->id));
		$newtransfer = new TransferModel(NULL, array('post' => $newpost->id, 'table__sender' => '6', 'sender' => $character->id, 'table__recipient' => '6', 'recipient' => $recipient->id));
		foreach ($transfer as $i)
			new TransferitemModel(NULL, array('transfer' => $newtransfer->id, 'item' => $i[0]->id, 'stack' => $i[1]));
		$this->move('thread/view/' . $this->obj->id . '/last#post' . $newpost->id);
	}
}
