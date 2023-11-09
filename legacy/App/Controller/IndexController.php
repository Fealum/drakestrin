<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;

class IndexController extends Controller
{

	protected $stdaction = 'std';

	function std()
	{
		$this->obj = new \stdClass();
		$this->obj->postcount24h = new _list('post', 'time >= ' . (time() - 86400));
		$this->obj->postcount24h = (is_null($this->obj->postcount24h->data)) ? 0 : count($this->obj->postcount24h->data);
		$this->obj->postcount7d = new _list('post', 'time >= ' . (time() - 604800));
		$this->obj->postcount7d = (is_null($this->obj->postcount7d->data)) ? 0 : count($this->obj->postcount7d->data);
		$this->obj->word = new _list('dictionary', 'language = 2', array('rand,a', array('rand' => 'RAND()')), array(1, ''));
		$this->obj->word = $this->obj->word->data[0];
		$this->obj->news = new _list('post', 'thread = 2108', array('time,d', array('time' => 'time')), array(1, ''));
		$this->obj->news = $this->obj->news->data[0];
		$this->set('obj', $this->obj);
	}
}
