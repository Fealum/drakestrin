<?php
class ConversationController extends Controller {
	
	protected $stdaction = 'viewall';

	function std() {
		$this->viewall();
	}

	function viewall() {
		$this->obj = new _list('conversationfilter', '`user__1` = '.$this->user->id.' OR `user__2` ='.$this->user->id, array('time,d', array('time' => 'time_end')));
		$this->set('obj', $this->obj);
	}
	
	function view($user) {
		$user = Cache::_('UserModel', $user);
		if ($user->id == $this->user->id) $this->move('conversation');
		$this->obj = new _list('conversation', '(`user__sender` = '.$this->user->id.' AND `user__recipient` = '.$user->id.') OR (`user__recipient` ='.$this->user->id.' AND `user__sender` = '.$user->id.')', array('time,d', array('time' => 'time')));
		$markread = new _list('conversation', '`user__recipient` = '.$this->user->id.' AND `user__sender` = '.$user->id.' AND `view` = 0');
		if ($markread->data) foreach ($markread->data as $obj) $obj->update(array('view' => '1'));
		$this->set('convuser', $user);
		$this->set('obj', $this->obj);
	}
	
	function create($user) {
		$user = Cache::_('UserModel', $user);
		$this->set('obj', $this->obj);
		$conversation = array('user__sender' => $this->user->id, 'user__recipient' => $user->id, 'view' => '0', 'time' => time(), 'message' => $this->post('message'));
		if ($conversation['message'] && $conversation['user__recipient'] && $conversation['user_-recipient'] != $this->user->id) {
			$newconversation = new ConversationModel(NULL, $conversation);
			$this->move('conversation/view/'.$user->id);
		}
		$this->set('conversation', $conversation);
	}
}