<?php
class IndexController extends Controller {
	
	protected $stdaction = 'std';

	function std() {
		$this->obj = new stdClass();
		$this->obj->threads = new _list('thread', NULL, array('lastpost,d', array('lastpost' => 'post__last_time')), 50);
		$this->obj->postcount24h = new _list('post', 'time >= '.(time() - 86400));
		$this->obj->postcount24h = count($this->obj->postcount24h->data);
		$this->obj->postcount7d = new _list('post', 'time >= '.(time() - 604800));
		$this->obj->postcount7d = count($this->obj->postcount7d->data);
		$this->set('viewed', $this->session->viewed);
		$this->set('obj', $this->obj);
	}

	function online() {
		$this->obj = new _list('online');
		$this->set('obj', $this->obj);
	}

	function statistics() {
		
	}
}