<?php

namespace sqwrapper;

class Column {
	public $formtype;
	public $dbtype;
	public $name;
	public $unique;
	public $inserthook;

	public $getpdo;


	public function __construct($name, $formtype, $dbtype, $getpdo = NULL) {
		$this->name = $name;
		$this->formtype = $formtype;
		$this->dbtype = $dbtype;

		$this->unique = null;
		$this->inserthook = null;
		$this->insertvalidate = null;

		if (is_callable($getpdo)) {
			$this->getpdo = $getpdo;
		}
		else {
			$this->getpdo = function() { return DB::connect(); };
		}
	}

	public function unique() {
		$this->unique = true;
		return $this;
	}

	public function noform() {
		$this->formtype = NULL;
		return $this;
	}

	public function currenttime() {
		$this->inserthook = function($v) {
			return date('Y-m-d H:i:s');
		};
		return $this;
	}

	public function setdefault($value) {
		$local = $value;
		$this->inserthook = function($v) use ($local) {
			return $local;
		};
		return $this;
	}

	public function setinserthook($f) {
		$this->inserthook = $f;
		return $this;
	}
	public function setinsertvalidate($f) {
		$this->insertvalidate = $f;
		return $this;
	}

	public function autoincrement() {
		$this->increment = true;
		$this->inserthook = function($v) {
			$db = $this->getpdo->__invoke();

			return DB::getmaxid($db, $v['table']) + 1;
		};
		return $this;
	}	

	public function getschema() {
		$schema = sprintf("`%s` `%s` not null", $this->name, $this->dbtype);
		if ($this->unique) {
			$schema .= " unique";
		}

		return $schema;
	}
}
