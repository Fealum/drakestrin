<?php
class CompanyController extends Controller {

	protected $stdaction = 'viewall';

	function std() {
		$this->viewall();
	}
	
	function viewall() {
	    $this->obj = new _list('company', NULL, array('name,a', array('name' => 'name')));
	    $this->set('obj', $this->obj);
	}
	
	function view($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		$this->set('obj', $this->obj);
	}
	
    function worker($id = 1) {
	    $this->obj = Cache::_('Company_workerModel', $id);
	    $this->onlinelocation(0, $this->obj->id);
	    $this->set('obj', $this->obj);
	    $workload = 0;
	    foreach ($this->obj->labour_active as $labour) 
	        $workload = $workload + (1 / $labour->labour->workload) * $labour->instances;
	    $this->set('workload', $workload);
	    $labours = new _list('labour', 'workload >= '.(1 / (1 - $workload)).' AND type <= '.$this->obj->type);
	    $possiblelabours = [];
	    foreach ($labours->data as $labourkey => $thislabour) {
	        $possible = true;
	        foreach ($thislabour->labour_component as $component) {
	            if ($component->type == 2) 
	                continue;
	            if (Items::availability($component->item, $this->obj->company) < $component->quantity) {
	                $possible = false;
	                break;
	            }
	        }
	        if($possible == true)
	            $possiblelabours[] = $thislabour;
	    }
	    $this->set('labours', $possiblelabours);

	    $assignlabour = array('company_worker' => $this->obj->id, 'labour' => $this->post('labour'), 'since' => time(), 'prodas' => $this->post('prodas'), 'prodas_value' => $this->post('prodas_value'), 'quantity' => $this->post('quantity'), 'quantity_count' => round($this->post('quantity_count')), 'instances' => round($this->post('instances')));
	    if ($this->post('assignlabour')) {
	        if ($this->obj->company->character->user != $this->user) {
	            $this->setnotice('company_worker_assignlabour_nopermission', 'error');
	            $this->move('company/worker/'.$this->obj->id);
	            exit;
	        }
	        $chosenlabour = new LabourModel($assignlabour->labour);
	        $assignlabour['until'] = $assignlabour['since'] + $chosenlabour->duration;
	        $missingresources = false;
	        $missingtools = false;
	        $possible = true;
	        foreach ($chosenlabour->labour_component as $component) {
	            if ($component->type == 2)
	                continue;
	            if (Items::availability($component->item, $this->obj->company) < $component->quantity) {
                    $possible = false;
                    break;
                }
	        }
	        if ($possible == false) {
	            $this->setnotice('company_worker_assignlabour_noresources', 'error');
	            $this->move('company/worker/'.$this->obj->id);
	            exit;
	        }
	        foreach ($chosenlabour->labour_component as $component) {
	            if ($component->type == 2)
	                continue;
                $componententity = Items::getEntity($component->item, $this->obj->company, $component->quantity, -2);
                var_dump($componententity);
                echo "Test";
                // Falls Werkzeug: relabeln als -3 (in Benutzung)
                if ($component->type == 1)
                    $usedtools = Items::transfer($componententity, $this->obj->company, $this->obj->company, -3);
                // var_dump($usedtools);
                // Falls Rohstoff: Löschen bzw. reduzieren
                else {
                    foreach ($componententity as $thisentity) {
                        if($thisentity[0]->item->stackable != 1)
                            $thisentity[0]->delete();
                        else
                            Items::changeInventorystack($thisentity[0], 0 - $thisentity[1]);
                    }
	            }
	        }

	        if ($assignlabour['quantity'] == '0') 
	            $assignlabour['quantity'] = $assignlabour['quantity_count'];
	        unset($assignlabour['quantity_count']);
	        if ($assignlabour['prodas'] == '0')
	            $assignlabour['prodas'] = $assignlabour['prodas_value'];
	        unset($assignlabour['prodas_value']);
	        if ($assignlabour['instances'] > floor((1 - $workload) * $chosenlabour->workload))
	            $assignlabour['instances'] = floor((1 - $workload) * $chosenlabour->workload);
	        $assignlabour['nextinsta'] = 0;
	        // $newlabour = new Labour_activeModel(NULL, $assignlabour);
	        // $this->_view->clear('worker', $this->obj->id);
	        //$this->setnotice('company_worker_assignlabour', 'success');
	        //$this->move('company/worker/'.$this->obj->id);
	    }
//	    $this->cacheid($this->obj->id);
	}
	
	function hire($id = 1, $type = 1) {
		$this->obj = Cache::_($this->_model, $id);
		if ($this->obj->character->user != $this->user) {
			$this->setnotice('company_hire_nopermission', 'error');
			$this->move('company/view/'.$this->obj->id);
			exit;
		}
		$type = (int)$type;
		$type = ($type > 0 && $type < 6) ? $type : 1;
		if ($type == 5) {
			$clerks = new _list('company_worker', 'type = 5 AND company = '.$this->obj->id);
			if (!is_null($clerks->data)) {
				$this->setnotice('company_hire_alreadyclerk', 'error');
				$this->move('company/view/'.$this->obj->id);
				exit;
			}
		}
		$firstname = array("Verion", "Limnas", "Kartrim", "Parnyas", "Hirion", "Malnaetos", "Wayront", "Emmant", "Piritugd", "Lywin", "Kamren");
		$lastname = array("Syrwantal", "Karimtelmar", "Vincis", "Aralyis", "Ewentem", "Ionaer", "Sayarmel");
		$workername = $firstname[mt_rand(0, count($firstname) - 1)].' '.$lastname[mt_rand(0, count($lastname) - 1)];
		$newworker = new Company_workerModel(NULL, array('name' => $workername, 'type' => $type, 'company' => $this->obj->id, 'hired' => time(), 'paid' => time()));
		$this->setnotice('company_hire', 'success', array('workername' => $workername));
		$this->move('company/view/'.$this->obj->id);
	}
	
	function killworker($arbeiter,$besitzer) {
	    global $db_zugriff, $n;
	    $lohnarray = array("A"=>3,"B"=>3,2=>4,3=>5,4=>6);
	    $data = $db_zugriff->query_first("SELECT a.name AS name, a.lohnerhalt AS lohnerhalt, a.typ AS typ, s.shopid AS shopid FROM bb".$n."_arbeiter AS a, bb".$n."_shops AS s WHERE (a.id LIKE ".$arbeiter." AND a.betrieb LIKE s.shopid AND s.besitzerid LIKE ".$besitzer.")");
	    if($data[name] && $data[shopid]) {
	        $diffzeit = time() - $data[lohnerhalt];
	        $lohn = 0;
	        if($diffzeit >= 172800){
	            $lohnzeit = floor($diffzeit / 2592000);
	            $lohn = $lohnzeit * $lohnarray[$data[typ]];
	            $shopgeld = geldstand($data[shopid],2);
	            if($shopgeld < $lohn) {
	                $restlohn = $lohn - $shopgeld;
	                $userstand = geldstand($besitzer);
	                if($userstand < $restlohn) $restlohn = $userstand;
	                transfer($restlohn,$besitzer,0,0,5,1);
	            }
	            transfer($lohn,$data[shopid],2,0,5,1);
	        }
	        mysql_query("DELETE FROM bb".$n."_arbeiter WHERE id = '$arbeiter'");
	        $result = array($data[name],$lohn,$restlohn,$data[shopid]);
	    }
	    else $result = 0;
	    return $result;
	}
	
	function fire($id = 1) {
		$this->obj = Cache::_('Company_workerModel', $id);
		if ($this->obj->company->character->user != $this->user) {
			$this->setnotice('company_fire_nopermission', 'error');
			$this->move('company/view/'.$this->obj->company->id);
			exit;
		}
		$timediff = time() - $this->obj->paid;
		$salary = 0;
		if ($timediff >= 172800) {
		    $salarytime = floor($timediff / 2592000);
		    $salary = $salarytime * (($worker->type == 1) ? 3 : ($worker->type + 1));
		    
		}
		$this->obj->delete();
		$this->setnotice('company_fire', 'success');
		$this->move('company/view/'.$this->obj->company->id);
	}
	
	function pay($id = 1) {
		$this->obj = Cache::_($this->_model, $id);
		if ($this->obj->character->user != $this->user) {
			$this->setnotice('company_pay_nopermission', 'error');
			$this->move('company/view/'.$this->obj->id);
			exit;
		}
		$balance = Items::availability(1, $this->obj->id, -1);
		if (is_null($balance)) {
			$this->setnotice('company_pay_nomoney', 'error');
			$this->move('company/view/'.$this->obj->id);
			exit;
		}
		$paid = array('paid' => 0, 'sumpaid' => 0, 'unpaid' => 0);
		foreach ($this->obj->company_worker as $worker) {
			$timespay = floor((time() - $worker->paid) / 2592000);
			$pay = 10000 * $timespay * (($worker->type == 1) ? 3 : ($worker->type + 1));
			if ($pay == 0) continue;
			if ($balance->stack - $pay - $paid['sumpaid'] > 0) {
				$paid['sumpaid'] += $pay;
				$paid['paid']++;
				$worker->update(array('paid' => ($worker->paid + ($timespay * 2592000))));
			}
			else
				$paid['unpaid']++;
		}
		$balance->update(array('stack' => $balance->stack - $paid['sumpaid']));
		$this->setnotice('company_pay', 'info', $paid);
		$this->move('company/view/'.$this->obj->id);
	}
}