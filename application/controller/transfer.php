<?php
class TransferController extends Controller {

	protected $stdaction = 'view';

	function std() {
		$this->view(1, 1);
	}

	function transfer($id = 1) {
		$this->obj = Cache::_('ThreadModel', $id);
		$this->set('obj', $this->obj);
		$post = array('thread' => $this->obj->id, 'user' => 2, 'character' => 3, 'time' => time(), 'message' => '', 'smilies' => 0, 'signature' => 0, 'ip' => $_SERVER['REMOTE_ADDR']);
		$transfer = array('inventory' => $this->post('inventory'), 'inventorystack' => $this->post('inventorystack'), 'recipient' => $this->post('recipient'));
		$character = Cache::_('CharacterModel', $this->post('character'));
		$recipient = Cache::_('CharacterModel', $transfer['recipient']);
		if (Permission::getPermission($this->obj, 'transfer') == 0 || !$transfer['inventory'] || $character->user->id != $this->user->id || !$recipient->id || $recipient->id == $character->id) {
			$this->setnotice('transfer_transfer_nopermission', 'error');
			$this->move('thread/view/'.$this->obj->id);
			exit;
		}
		$transferarray = array();
		foreach ($transfer['inventory'] as $k => $i) 
		    $transferarray[] = array(Cache::_('ItemModel', $transfer['inventory'][$k]), $transfer['inventorystack'][$k]);
			$transfer = Items::transfer($transferarray, $character, $recipient);
		if (!is_array($transfer)) {
			$this->setnotice('transfer_transfer_nopermission', 'error');
			$this->move('thread/view/'.$this->obj->id.'/last#post'.$newpost->id);
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
		$this->move('thread/view/'.$this->obj->id.'/last#post'.$newpost->id);
	}
}