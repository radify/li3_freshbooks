<?php

namespace li3_freshbooks\tests\cases\extensions\data\source\http;

use lithium\data\Connections;
use li3_freshbooks\extensions\adapter\data\source\http\Freshbooks;

class FreshbooksTest extends \lithium\test\Unit {

	public $source;

	protected $_backup = array();

	protected $_testConfig = array(
		'login' => 'test',
		'password' => '123',
		'socket' => 'li3_freshbooks\tests\mocks\data\source\http\MockFreshbooksSocket'
	);

	public function setUp() {
		$this->_backup['connections'] = Connections::config();

		Connections::reset();
		$this->source = new Freshbooks(array('socket' => false));

		Connections::config(array(
			'mocked' => array(
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