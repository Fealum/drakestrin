<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;
use Legacy\Library\Class\Permission;
use Legacy\App\Model\WordtypeModel;
use Legacy\App\Model\DictionaryModel;
use Legacy\App\Model\DictionarykeyModel;

class DictionaryController extends Controller
{

	protected $stdaction = 'viewall';

	function std()
	{
		$this->viewall();
	}

	function viewall($language_from = 2, $language_to = 1, $order = 'word,a')
	{
		$wordtypes = new _list('wordtype');
		$language_from = Cache::_('LanguageModel', $language_from);
		$language_to = Cache::_('LanguageModel', $language_to);
		$this->obj = new _list('dictionary', 'language = ' . $language_from, array($order, array('word' => 'LOWER(word)')));
		$this->set('wordtypes', $wordtypes);
		$this->set('language_from', $language_from);
		$this->set('language_to', $language_to);
		$this->set('obj', $this->obj);
	}

	function view($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}

	function create()
	{
		if (Permission::getPermission(NULL, 'createdictionary') == 0) {
			$this->setnotice('dictionary_create_nopermission', 'error');
			$this->move('dictionary');
		}

		if ($this->post('advanced-language-from') && $this->post('advanced')) {
			$lines = explode("\n", $this->post('advanced'));
			$languages = array('from' => $this->post('advanced-language-from'), 'to' => $this->post('advanced-language-to'));
			foreach ($lines as $line) {
				$entries = explode('.', $line);
				$movetoid = 0;
				foreach ($entries as $key => $entry) {
					if (trim($entry) == "") continue;
					$entry = explode(',', $entry);
					$language = ($key == 0) ? $languages['from'] : $languages['to'];
					$getwordtype = new WordtypeModel;
					$getwordtype->id_from_unique('code', trim($entry[1]));
					$dictionary = array('language' => $language, 'wordtype' => $getwordtype->id, 'word' => trim($entry[0]));
					$newdictionary = new DictionaryModel(NULL, $dictionary);
					if ($key == 0) $movetoid = $newdictionary->id;
					else {
						new DictionarykeyModel(NULL, array('dictionary__from' => $movetoid, 'dictionary__to' => $newdictionary->id));
						new DictionarykeyModel(NULL, array('dictionary__from' => $newdictionary->id, 'dictionary__to' => $movetoid));
					}
				}
			}
			$this->move('dictionary');
		} else {
			$dictionary = array('language' => $this->post('language'), 'wordtype' => $this->post('wordtype'), 'word' => $this->post('word'));
			if ($dictionary['language'] && $dictionary['wordtype'] && $dictionary['word']) {
				$newdictionary = new DictionaryModel(NULL, $dictionary);
				$this->move('dictionary/view/' . $newdictionary->id);
			}
		}

		$languages = new _list('language');
		$wordtypes = new _list('wordtype');
		$this->set('languages', $languages);
		$this->set('wordtypes', $wordtypes);
	}

	function edit($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission(NULL, 'editdictionary') == 0) {
			$this->setnotice('dictionary_edit_nopermission', 'error');
			$this->move('dictionary');
		}

		$dictionary = array('language' => $this->post('language'), 'wordtype' => $this->post('wordtype'), 'word' => $this->post('word'));
		if ($dictionary['language'] && $dictionary['wordtype'] && $dictionary['word']) {
			$this->obj->update($dictionary);
			$this->setnotice('dictionary_edit', 'success');
			$this->move('dictionary/view/' . $this->obj);
		}

		$languages = new _list('language');
		$wordtypes = new _list('wordtype');
		$this->set('languages', $languages);
		$this->set('wordtypes', $wordtypes);
	}

	function delete($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission(NULL, 'deletedictionary') == 0) {
			$this->setnotice('dictionary_delete_nopermission', 'error');
			$this->move('dictionary');
		}
		if ($this->post('delete') == 1) {
			$keys = new _list('dictionarykey', 'dictionary__from = ' . $this->obj->id . ' OR dictionary__to = ' . $this->obj->id);
			foreach ($keys->data as $value) $value->delete();
			$this->obj->delete();
			$this->setnotice('dictionary_delete', 'success');
			$this->move('dictionary');
		}
	}

	function createkey($id = 1)
	{
		$this->obj = Cache::_($this->_model, $id);

		if (Permission::getPermission(NULL, 'createdictionary') == 0) {
			$this->setnotice('dictionary_create_nopermission', 'error');
			$this->move('dictionary');
		}

		$dictionarykey = array('dictionary__from' => $this->obj, 'dictionary__to' => $this->post('word'));
		if ($dictionarykey['dictionary__from'] && $dictionarykey['dictionary__to']) {
			$keys = explode(',', $this->post('word'));
			foreach ($keys as $i) {
				new DictionarykeyModel(NULL, array('dictionary__from' => $this->obj, 'dictionary__to' => trim($i)));
				if ($this->post('bijective') == 1) new DictionarykeyModel(NULL, array('dictionary__from' => trim($i), 'dictionary__to' => $this->obj));
			}
			$this->move('dictionary/view/' . $this->obj);
		}
		$this->set('obj', $this->obj);
	}

	function deletekey($id = 1)
	{
		$this->obj = Cache::_('DictionarykeyModel', $id);
		$this->set('obj', $this->obj);
		if (Permission::getPermission(NULL, 'deletedictionary') == 0) {
			$this->setnotice('dictionary_delete_nopermission', 'error');
			$this->move('dictionary');
		}
		if ($this->post('delete') == 1) {
			$moveto = $this->obj->dictionary__from->id;
			$this->obj->delete();
			$this->setnotice('dictionary_delete', 'success');
			$this->move('dictionary/view/' . $moveto);
		}
	}

	function ajax__getwords()
	{
		$query = addslashes($this->post('q'));
		$this->obj = new _list('dictionary', 'word LIKE "%' . $query . '%" OR id = "' . $query . '"', array('wordlength,a', array('wordlength' => 'LENGTH(word)')), array(10, null));
		$this->set('query', $this->post('q'));
		$this->set('obj', $this->obj);
	}
}
