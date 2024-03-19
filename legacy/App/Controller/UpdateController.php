<?php

namespace Legacy\App\Controller;

use Legacy\Library\Class\Controller;
use Legacy\Library\Class\_list;
use Legacy\Library\Class\Cache;

class UpdateController extends Controller
{

	protected $stdaction = 'std';

	function std($board = 0)
	{
		if ($this->user->id == 37) {
			header('Content-Type: text/markdown');
			header('Content-Disposition: attachment; filename="dra_posts_board_' . $board . '.txt"');
			header('Pragma: no-cache');
			header('Expires: 0');

			$this->outputThreads((int) $board);
		}
	}

	/**
	 * Laeuft durch die Daten eines Listentyps und kodiert sie, falls noetig, als UTF8 neu. 
	 * $name ist dabei der Name der Tabelle; $objects ein Array, das alle zu kodierenden
	 * Felder beinhaltet.
	 * @param string $name
	 * @param array $objects
	 */
	function correctutf8($name, $limit__count, $limit__offset, $objects)
	{
		$gothrough = new _list($name, NULL, NULL, array($limit__count, $limit__offset));
		foreach ($gothrough->data as $cur) {
			$update = false;
			foreach ($objects as $i) {
				if ($cur->$i != utf8ify($cur->$i)) {
					$update = true;
					break;
				}
			}
			if ($update == true) {
				echo '<pre>Korrigiert: ' . $cur->id . '</pre>';
				$updatearray = array();
				foreach ($objects as $i) $updatearray[$i] = utf8ify($cur->$i);
				$cur->update($updatearray);
			}
		}
	}

	function outputThreads($board)
	{
		$board = Cache::_('BoardModel', $board);
		echo '# Forum ' . $board . ': ' . $board->name . PHP_EOL . PHP_EOL;
		if ($board->thread) {
			foreach ($board->thread as $thread) {
				echo '## Thema ' . $thread . ': ' . $thread->name . PHP_EOL . PHP_EOL;
				if ($thread->post) {
					foreach ($thread->post as $post) {
						echo '### Beitrag von ' . $post->character->name . ', ' . date('Y-m-d H:m:s', $post->time) . PHP_EOL . PHP_EOL;
						echo $post->message . PHP_EOL . PHP_EOL;
					}
				}
			}
		}

		if ($board->board__1) {
			foreach ($board->board__1 as $subBoard) {
				$this->outputThreads($subBoard->id);
			}
		}
	}

	function makegroup()
	{
		$gothrough = new _list('user');
		$standardgroup = Cache::_('GroupModel', 1);
		foreach ($gothrough->data as $cur) {
			if (!$cur->group) {
				$cur->createrelation('group', $standardgroup);
				echo '<pre>Gruppe eingerichtet fuer ' . $cur->name . '</pre>';
			}
		}
	}

	function correctthreadcount($limit__count, $limit__offset)
	{
		$gothrough = new _list('thread', NULL, NULL, $limit__count, $limit__offset);
		foreach ($gothrough->data as $obj) {
			$count = count($obj->post);
			if ($count != $obj->post__total) {
				echo '<pre>Thema ' . $obj->name . ': ' . $count . ' (neu) != ' . $obj->post__total . ' (alt)</pre>';
				$obj->update(array('post__total' => $count));
			}
		}
	}

	function correctthreadlastpost($limit__count, $limit__offset)
	{
		$gothrough = new _list('thread', 'post__last = 0', NULL, $limit__count, $limit__offset);
		foreach ($gothrough->data as $obj) {
			$post = $obj->post[count($obj->post) - 1];
			echo '<pre>Thema ' . $obj->name . ': ' . $post->id . ', ' . $post->time . ', ' . count($obj->post) . '</pre>';
			$obj->update(array('post__last' => $post->id, 'post__last_time' => $post->time));
		}
	}

	function correcttotals($name, $limit__count, $limit__offset)
	{
		$gothrough = new _list($name, NULL, NULL, $limit__count, $limit__offset);
		foreach ($gothrough->data as $obj) {
			$postnumber = 0;
			foreach ($obj->thread as $i)
				$postnumber = $postnumber + $i->post__total;
			if ($postnumber != $obj->post__total) echo '<pre>Forum ' . $obj->name . ': ' . $postnumber . ' != ' . $obj->post__total . '</pre>';
			if (count($obj->thread) != $obj->thread__total) echo '<pre>Forum ' . $obj->name . ' (Threads): ' . count($obj->thread) . ' != ' . $obj->thread__total . '</pre>';
			if ($postnumber != $obj->post__total || count($obj->thread) != $obj->thread__total) {
				$obj->update(array('post__total' => $postnumber, 'thread__total' => count($obj->thread)));
			}
		}
	}
}
