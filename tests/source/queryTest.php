<?php

require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__) . '/../../source/query.class.php';

/**
 * Test class for query.
 * Generated by PHPUnit on 2011-01-26 at 19:39:13.
 */
class queryTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var query
	 */
	protected $q;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->q = new query;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
		$this->q = null;
	}
	/**
	 * Test adding a single table to the query
	 */
	public function testAddTable(){
		$this->q->table('test');
		$this->assertEquals(array('table'=>'test', 'alias'=>null), $this->q->tables[0]);
	}
	public function testAddColumn(){
		$this->q->column('test');
		$expected = array(
			'column'=>'test',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testNULLColumn(){
		$this->q->column(null);
		$expected = array(
			'column'=>'NULL',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testFALSEColumn(){
		$this->q->column(false);
		$expected = array(
			'column'=>'FALSE',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testTRUEColumn(){
		$this->q->column(true);
		$expected = array(
			'column'=>'TRUE',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testMathColumn(){
		$this->q->column('1 = 1');
		$expected = array(
			'column'=>'1 = 1',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	public function testFunctionColumn(){
		$this->q->column('COUNT(*)');
		$expected = array(
			'column'=>'COUNT(*)',
			'alias'=>null
		);
		$this->assertEquals($expected, $this->q->columns[0]);
	}
	/**
	 * Test chaining of functions
	 */
	public function testChaining(){
		$this->q->table('test')
				->column('test_2', 'test')
				->table('test_3', 'test_2');
		
		$expected_tables = array(
			array(
				'table'=>'test',
				'alias'=>null
			),
			array(
				'table'=>'test_3',
				'alias'=>'test_2'
			)
		);
		$expected_columns = array(
			array(
				'column'=>'test_2',
				'alias'=>'test'
			)
		);
		$this->assertEquals($expected_tables, $this->q->tables);
		$this->assertEquals($expected_columns, $this->q->columns);
	}
	public function testFirstWhere(){
		$this->q->where('1', '1');
		$expected = array(
			'column'=>'1',
			'where'=>'1',
			'comparison'=>'=',
			'type'=>null,
			'escape'=>true,
		);
		$this->assertEquals($expected, $this->q->wheres[0]);
	}
	public function testClearWhere(){
		$this->q->where('1', '1')->where('2', '2');
		$expected = array(
			'column'=>'2',
			'where'=>'2',
			'comparison'=>'=',
			'type'=>null,
			'escape'=>true,
		);
		$this->assertEquals($expected, $this->q->wheres[0]);
	}
	public function testAndWhere(){
		$this->q->where('1', '1')->and_where('2', '2');
		$expected =  array(
			array(
				'column'=>'1',
				'where'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'2',
				'where'=>'2',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>true,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testOrWhere(){
		$this->q->where('1', '1')->or_where('2', '2');
		$expected =  array(
			array(
				'column'=>'1',
				'where'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'2',
				'where'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>true,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testAndWhereOrWhere(){
		$this->q->where('1', '1')
				->and_where(true, true)
				->and_where(null, null, 'iS')
				->or_where('2', '2', '=', false);
		$expected =  array(
			array(
				'column'=>'1',
				'where'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'column'=>'TRUE',
				'where'=>'TRUE',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>true,
			),
			array(
				'column'=>'NULL',
				'where'=>'NULL',
				'comparison'=>'IS',
				'type'=>'AND',
				'escape'=>true,
			),
			array(
				'column'=>'2',
				'where'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>false,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
	public function testBrackets(){
		$this->q->where('1', '1')
				->begin_and()
				->and_where(true, true)
				->begin_or()
				->and_where(null, null, 'iS')
				->end_or()
				->end_or()
				->or_where('2', '2', '=', false);
		$expected =  array(
			array(
				'column'=>'1',
				'where'=>'1',
				'comparison'=>'=',
				'type'=>null,
				'escape'=>true,
			),
			array(
				'bracket'=>'OPEN',
				'grouping'=>'AND'
			),
			array(
				'column'=>'TRUE',
				'where'=>'TRUE',
				'comparison'=>'=',
				'type'=>'AND',
				'escape'=>true,
			),
			array(
				'bracket'=>'OPEN',
				'grouping'=>'OR'
			),
			array(
				'column'=>'NULL',
				'where'=>'NULL',
				'comparison'=>'IS',
				'type'=>'AND',
				'escape'=>true,
			),
			array(
				'bracket'=>'CLOSE',
			),
			array(
				'bracket'=>'CLOSE',
			),
			array(
				'column'=>'2',
				'where'=>'2',
				'comparison'=>'=',
				'type'=>'OR',
				'escape'=>false,
			)
		);
		$this->assertEquals($expected, $this->q->wheres);
	}
}