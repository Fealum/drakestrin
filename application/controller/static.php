<?php
class StaticController extends Controller {

	protected $stdaction = 'help';

	function std() {
		$this->help();
	}

	function help() {
		$this->cacheid('help');
	}

	function terms() {
		$this->cacheid('terms');
	}

	function legal() {
		$this->cacheid('legal');
	}
}