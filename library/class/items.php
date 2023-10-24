<?php
class Items {
    /**
     * @return array result
     * @param entity int
     * @param item int
     * @param owner Object
     * @param status int
     * @desc Ermittelt ein Objekt im Inventar und gibt es zurück.
     **/
    function getInventory($item, $owner, $status=null) {
        global $tables;
        // Objekt suchen
        $entity = new _list('inventory', 'item = '.(int)$item.' AND table__owner = '.array_search($owner->table, $tables).' AND owner = '.$owner->id.((!is_null('$status')) ? ' AND wear = '.(int)$status : ''));
        // Wenn Liste gefunden, dann erstes Objekt daraus nehmen
        if ($entity != NULL) return $entity->data[0];
        // sonst false zurückgeben
        else return false;
    }
    
    /**
     * @return boolean
     * @param $value int
     * @param $entity int
     * @param $item int
     * @param $ownertype int
     * @param $ownerid int
     * @param $status int
     * @desc Verändert den Stack eines Inventarobjekts um $value - benötigt $object und $value.
     **/
	private static function changeInventorystack($object, $value = 1) {
		if (!is_object($object)) return false;
		if ($object->item->stackable != 1) return false;
		if ($object->stack + $value > 0) $object->update(array('stack' => $object->stack + value));
		elseif ($object->stack + $value == 0) $object->delete();
		else return false;
	}

	/**
	 * @desc Bewegt Entitäten von A (Sender) zu B (Empfänger). Object besteht aus einem Array (id, stack)
	 **/
	public static function transfer($object, $from, $to, $tostatus = NULL) {
		global $tables;
        // Wenn Sender und Empfänger null: abbrechen
        if (is_null($from) && is_null($to)) return 'FROM AND TO NOT SET';
		// Wenn keine Charaktere aus IDs geladen werden konnten: Fehlermeldung
		if ((!is_null($from) && !$from->id) || (!is_null($to) && !$to->id)) return 'FROM OR TO WRONG';
		// Falls mehrere Objekte: Alle abarbeiten; Fehlermeldungen sammeln
		if (is_array($object[0])) {
			$return = array();
			$errors = array('FOLLOWING ERRORS:');
			foreach ($object as $i) {
				$thistransfer = self::transfer($i, $from, $to, $tostatus);
				if (is_array($thistransfer)) $return[] = $thistransfer;
				else $errors[] = $thistransfer;
			}
			return (count($return) > 0) ? $return : join('   ', $errors);
		}
		// Wenn $object[0] eine Nummer ist und keine Entität
		if (is_numeric($object[0]))
		    // Wenn kein Sender: Gegenstand als Item holen, sonst aus Inventar
			$object[0] = (is_null($from)) ? Cache::_('ItemModel', $object[0]) : Cache::_('InventoryModel', $object[0]);
		// Wenn Sender vorhanden und Gegenstand nicht dem Sender gehört: Fehlermeldung zurück
		if (!is_null($from) && $object[0]->owner != $from) return 'FROM IS NOT OWNER';
		// Fall A: Item nicht stackable
		if ($object[0]->item->stackable != 1) {
		    
		}
		// Wenn Sender vorhanden und Objekt nicht stackable: Neuen Eigentümer des Objekts aktualisieren mit wear-Status $tostatus und Item des Objekts zurückgeben
		if (!is_null($from) && $object[0]->item->stackable != 1) {
		    // Falls Empfänger angegeben, an Empfänger überweisen
		    if (!is_null($to)) {
		      $object[0]->update(array('table__owner' => array_search($to->table, $tables), 'owner' => $to->id, 'wear' => (!is_null('$tostatus')) ? (int)$tostatus : 0));
		      return array($object[0]->item, 0);
		    }
		    // Sonst löschen
		    else {
		        $item = $object[0]->item;
		        $object[0]->delete();
		        return array($item, $object[1]);
		    }
		}
		// Falls Objektstack nicht gesetzt, dann auf 1 setzen, sonst units rausrechnen, falls vorhanden.
		$object[1] = (!isset($object[1])) ? 1 : $object[0]->undounitary($object[1]);
		// Falls Objektstack kleiner gleich null, dann auf 1 setzen
		if ($object[1] <= 0)
			$object[1] = 1;
		// Sonst: Falls Sender vorhanden und mehr Objekte verschoben werden sollen als beim Sender sind, dann Objektanzahl auf Objekte beim Sender setzen 
		elseif (!is_null($from) && $object[1] > $object[0]->stack)
			$object[1] = $object[0]->stack;
		// Falls Empfänger vorhanden: Objekt des zu bewegenden Typs beim Empfänger 
		if (!is_null($to))
		    $invatrecipient = self::getInventory($object[0]->item->id, $to, $tostatus);
		else 
		    $invatrecipient = NULL;
		// Wenn Sender vorhanden und Anzahl der zu übertragenden Objekte der Anzahl der beim Sender vorhandenen Objekte entspricht 
		// und es keine Objekte des Typs beim Empfänger gibt: Gesamten Stapel dem Empfänger überschreiben
		if (!is_null($from) && $object[1] == $object[0]->stack && $invatrecipient == NULL) {
		    $object[0]->update(array('table__owner' => array_search($to->table, $tables), 'owner' => $to->id, 'wear' => (!is_null('$tostatus')) ? (int)$tostatus : 0));
			return array($object[0]->item, $object[1]);
		}
		// Sonst: Falls Sender vorhanden und gesamter Stapel des Senders übertragen werden soll, aber Objekte des Typs bereits beim Empfänger exxistieren, oder
		// falls Sender nicht vorhanden, aber Objekte des Typs bereits beim Empfänger existieren: Beim Empfänger Stack um Objektanzahl erhöhen, und falls Sender
		// vorhanden: Stapel beim Sender löschen
		elseif ((!is_null($from) && $object[1] == $object[0]->stack && !is_null($invatrecipient)) || (is_null($from) && !is_null($invatrecipient))) {
		    self::changeInventorystack($invatrecipient, $object[1]);
			if (!is_null($from)) {
				$item = $object[0]->item;
				$object[0]->delete();
			}
			else
				$item = $object[0];
			return array($item, $object[1]);
		}
		// Sonst: Falls der zu übertragende Stapel kleiner ist als die vorhandenen Objekte ...
		elseif ($object[1] < $object[0]->stack) {
		    // Wenn keine Objekte des Typs beim Empfänger, neue Entität im Inventar anlegen.
			if ($invatrecipient == NULL)
				$newinv = new InventoryModel(NULL, array('item' => $object[0]->item->id, 'stack' => $object[1], 'wear' => (!is_null('$tostatus')) ? (int)$tostatus : 0, 'table__owner' => array_search($to->table, $tables), 'owner' => $to->id));
			// Sonst: Stack im Inventar des Empfängers erhöhen.
			else
			    self::changeInventorystack($invatrecipient, $object[1]);
			// Stack erniedrigen beim Sender.
			self::changeInventorystack($object[0], 0 - $object[1]);
			return array($object[0]->item, $object[1]);
		}
	}

	/**
	 * @desc Gibt eine natürliche Zahl zurück, die beschreibt, wie viel eines bestimmten Gegenstands mit welchem Status im Besitz einer Entität ist.
	 **/
	public static function availability($object, $owner, $wear = -2) {
	    global $tables;
	    if (!is_int($object)) $object = $object->id;
        $obj = new _list('inventory', 'item = '.$object.' AND table__owner = '.array_search($owner->table, $tables).' AND owner = '.$owner->id.' AND wear = '.$wear);
        $number = 0;
        foreach ($obj->data as $i) {
            if($i->item->stackable == 1) $number++;
            else $number += $i->stack;
        }
        return $number;
	}
	
	/**
	 * @desc Gibt ein Inventory-Array (id, stack) zurück, das $number viele Inventory-Entitäten des Typs $item zurückgibt, bei denen der Eigentümer $owner und der Status $wear ist.
	 **/
	public static function getEntity($item, $owner, $number = 1, $wear = -2) {
	    global $tables;
	    if (!is_int($item)) $item = $item->id;
	    $obj = new _list('inventory', 'item = '.$item.' AND table__owner = '.array_search($owner->table, $tables).' AND owner = '.$owner->id.' AND wear = '.$wear);
	    $result = [];
	    foreach ($obj->data as $i) {
	        if($i->item->stackable == 1) {
	            $result[] = [$i->id, 1];
	            $number--;
	        }
	        else {
	            if ($i->stack > $number) {
	                $result[] = [$i->id, $number];
	                $number = 0;
	            }
	            else {
	                $result[] = [$i->id, $i->stack];
	                $number = $number - $i->stack;
	            }
	        }
	        if ($number == 0) 
	            break;
	    }
	    return $result;
	}
	
	/**
	 * @desc Gibt eine natürliche Zahl zurück, die der maximalen Instanzanzahl einer Handlung, beschränkt durch die gegebenen Güter, entspricht.
	 **/
	private static function maxInstance($objtrans, $owner) {
		$number = -1;
		foreach($objtrans as $object) {
			$thisnumber = self::availability($object[0], $owner);
			$thisnumber = $thisnumber / $object[1];
			if($number == -1 || $thisnumber < $number) $number = $thisnumber;
		}
		return $number;
	}
	
	public static function work() {
		$expired = new _list('labour_active', 'until <= '.time(), array('until,a', array('until' => 'until')));
		if (is_null($expired->data)) return FALSE;
		foreach ($expired->data as $exp) {
			if ($exp->company_worker->paid <= (time() - 7776000))
				continue;
			$arbeit = $db_zugriff->query_first("SELECT * FROM bb".$n."_handlungen WHERE id = '$exp[handlungsid]'");
			$components = array(0 => array(), 1 => array(), 2 => array());
			foreach ($exp->labour->labour_component as $c) {
				$components[$c->type][] = array($c->item, $c->quantity);
			}
			$processes = ceil((time() - $exp->until) / $exp->labour->duration);
			if ($exp->quantity != -1 && $exp->quantity <= $processes) {
				$processes = $exp->quantity;
				$finished = true;
			}
			else
				$finished = false;
			$totalprocesses = $processes * $exp->instances;
			if (!empty($components[0])) {
				$maxprocesses = self::maxInstance($components[0], $exp->company_worker->company);
				if($totalprocesses > $maxprocesses) {
					$totalprocesses = $maxprocesses;
					$processes = floor($totalprocesses / $exp->instances);
				}
				objtrans(makeobjArray($arbeit["ergebnis"], $totalprocesses), 0, 5, $exp->company_worker->company, 2);
				$rohstoffe = objReturn(makeobjArray($arbeit["benrohstoff"], $totalprocesses), $exp->company_worker->company);
				objtrans($rohstoffe, $exp->company_worker->company, 2, 0, 5);
			}
			else objtrans(makeobjArray($arbeit["ergebnis"], $totalprocesses), 0, 5, $exp->company_worker->company, 2);
			$neuezeit = $exp["bis"] + ($arbeit["benzeit"] * ($totalprocesses + 1));
			if ($finished) {
				objRelabel(objReturn(makeobjArray($arbeit["benwerkzeug"]), $exp->company_worker->company, "B"), $exp->company_worker->company, 2, "P");
				mysql_query("DELETE FROM bb".$n."_momtaetig WHERE id = ".$exp["id"]);
			}
			else {
				$neuanzahl = ($exp["anzahl"] != "X") ? ($exp["anzahl"] - $processes) : "X";
				mysql_query("UPDATE bb".$n."_momtaetig SET anzahl = '$neuanzahl', bis = ".$neuezeit.", instances = ".$exp->instances.", nextinsta = ".$exp["nextinsta"]." WHERE id = ".$exp["id"]);
			}
		}
	}
}