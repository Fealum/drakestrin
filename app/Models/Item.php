<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'dra_item';

    public $timestamps = false;

    protected $casts = [
        'stackable' => 'integer',
        'weight' => 'integer',
        'img' => 'integer',
    ];

    public function makeunitary(int|string|null $stack): int|string
    {
        $stack = $stack ?? 0;
        $unit = $this->unit;
        $weight = (int) $this->weight;

        if ($unit === 'g') {
            $fix = [1000000 => 't', 1000 => 'kg', 1 => 'g'];
            $amount = (int) $stack * $weight;
        } elseif ($unit === 'l') {
            $fix = [100000 => 'hl', 1000 => 'l', 1 => 'ml'];
            $amount = (int) $stack;
        } elseif ($unit === 't') {
            $fix = [10000000 => 'tl', 10000 => 'tk', 1 => 'tn'];
            $amount = (int) $stack;
        } else {
            return $stack;
        }

        $result = '';

        foreach ($fix as $factor => $topUnit) {
            $lot = (int) floor($amount / $factor);
            $amount -= $lot * $factor;
            $result .= $lot ? $lot . $topUnit : '';
        }

        return $result;
    }

    public function undounitary(int|string|null $stack): int
    {
        $stack = $stack ?? 1;
        $unit = $this->unit;
        $weight = (int) $this->weight;

        if (is_numeric($stack)) {
            return (int) $stack;
        }

        if ($unit === 'g') {
            $fix = [1000000 => 't', 1000 => 'kg', 1 => 'g'];
        } elseif ($unit === 'l') {
            $fix = [100000 => 'hl', 1000 => 'l', 1 => 'ml'];
        } elseif ($unit === 't') {
            $fix = [10000000 => 'tl', 10000 => 'tk', 1 => 'tn'];
        } else {
            return (int) $stack;
        }

        $remaining = (string) $stack;
        $result = 0;

        foreach ($fix as $factor => $topUnit) {
            $pieces = explode($topUnit, $remaining, 2);

            if (isset($pieces[1]) && is_numeric($pieces[0])) {
                $result += (int) $pieces[0] * $factor;
                $remaining = $pieces[1];
            }
        }

        if ($unit === 'g' && $weight > 0) {
            $result = (int) floor($result / $weight);
        }

        return $result;
    }
}
