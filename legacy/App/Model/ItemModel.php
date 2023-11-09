<?php

namespace Legacy\App\Model;

use Legacy\Library\Class\Model;

class ItemModel extends Model
{

	protected $datatypes = array(
		'name' => 'string',
		'stackable' => 'int',
		'description' => 'string',
		'img' => 'int',
		'weight' => 'int',
		'unit' => 'string'
	);

	public function makeunitary($stack)
	{
		$unit = $this->unit;
		$weight = $this->weight;
		if ($unit == "g") $fix = array(1000000 => "t", 1000 => "kg", 1 => "g");
		elseif ($unit == "l") $fix = array(100000 => "hl", 1000 => "l", 1 => "ml");
		elseif ($unit == "t") $fix = array(10000000 => "tl", 10000 => "tk", 1 => "tn");
		else return $stack;
		if ($unit == "g") $amount = $stack * $weight;
		else $amount = $stack;
		$breakdown = [];
		$result = "";
		foreach ($fix as $factor => $topunit) {
			$breakdown[$topunit] = floor($amount / $factor);
			$amount = $amount - $breakdown[$topunit] * $factor;
		}
		foreach ($breakdown as $topunit => $lot) $result .= (!$lot) ? "" : $lot . $topunit;
		return $result;
	}

	public function undounitary($stack)
	{
		$unit = $this->unit;
		$weight = $this->weight;
		if ($unit == "g") $fix = array(1000000 => "t", 1000 => "kg", 1 => "g");
		elseif ($unit == "l") $fix = array(100000 => "hl", 1000 => "l", 1 => "ml");
		elseif ($unit == "t") $fix = array(10000000 => "tl", 10000 => "tk", 1 => "tn");
		else return $stack;
		if (is_numeric($stack)) return (int)$stack;
		$result = 0;
		foreach ($fix as $factor => $topunit) {
			$i = explode($topunit, $stack);
			if (isset($i[1])) {
				$stack = $i[1];
				$result += $i[0] * $factor;
			}
		}
		if ($unit == "g") $result = $result / $weight;
		return $result;
	}
}
