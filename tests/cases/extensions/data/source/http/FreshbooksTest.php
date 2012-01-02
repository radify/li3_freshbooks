<?php

namespace li3_freshbooks\tests\cases\extensions\data\source\http;

use lithium\data\Connections;
use li3_freshbooks\extensions\adapter\data\source\http\Freshbooks;
use li3_freshbooks\tests\mocks\extensions\data\source\http\MockFreshbooksSocket;

class FreshbooksTest extends \lithium\test\Unit {

	public $source;

	public $socket;

	protected $_backup = array();

	public function setUp() {
		$this->_backup['connections'] = Connections::config();
		Connections::reset();

		$this->socket = new MockFreshbooksSocket();
		$this->source = new Freshbooks(array(
			'login' => 'test',
			'password' => '123',
			'socket' => $this->socket
		));

		Connections::config(array(
			'freshbooks' => array(
				'object' => &$this->source,
				'adapter' => 'Freshbooks'
			)
		));
	}

	public function tearDown() {
		Connections::reset();
		Connections::config($this->_backup['connections']);
	}

	public function testConnection() {
		$result = $this->source->connect();
		$this->assertTrue($result);

		$result = $this->source->disconnect();
		$this->assertTrue($result);
	}

	public function testEnabled() {
		$result = Freshbooks::enabled();
		$this->assertTrue($result);

		$result = Freshbooks::enabled('arrays');
		$this->assertTrue($result);

		$result = Freshbooks::enabled('transactions');
		$this->assertFalse($result);

		$result = Freshbooks::enabled('booleans');
		$this->assertTrue($result);

		$result = Freshbooks::enabled('relationships');
		$this->assertFalse($result);
	}

	// public function testCast() {}
	// public function testRead() {}
}

?>