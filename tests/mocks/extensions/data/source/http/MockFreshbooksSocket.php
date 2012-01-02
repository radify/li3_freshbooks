<?php

namespace li3_freshbooks\tests\mocks\extensions\data\source\http;

class MockFreshbooksSocket extends \lithium\net\Socket {

	public $returnRead;

	protected $_data = null;

	public function open(array $options = array()) {
		parent::open($options);
		return true;
	}

	public function close() {
		return true;
	}

	public function eof() {
		return true;
	}

	public function read() {
		return $this->read;
	}

	public function write($data) {
		return $this->_data = $data;
	}

	public function timeout($time) {
		return true;
	}

	public function encoding($charset) {
		return true;
	}
}

?>