<?php
include '../vendor/autoload.php';
/**
 * Description of DvspUrlTest
 */
class DvspUrlTest extends PHPUnit_Framework_TestCase {
	public function testUrlRoute() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
	}

	public function testUrlRouteRes() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk/foobar");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('foobar', $url->res());
	}

	public function testUrlRouteGlq() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk:glq");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('glq', $url->glq());
	}

	public function testUrlRouteQuery() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk?query");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('query', $url->query());
	}

	public function testUrlRouteGlqRes() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk:glq/foobar");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('glq', $url->glq());
		$this->assertEquals('foobar', $url->res());
	}

	public function testUrlRouteGlqQuery() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk:glq?query");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('glq', $url->glq());
		$this->assertEquals('query', $url->query());
	}
	
	public function testUrlRouteGlqResQuery() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk:glq/foobar?query");
		$this->assertEquals(4, count($url->route()));
		$this->assertEquals('uk', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
		$this->assertEquals('uk', $url->route()[3]);
		$this->assertEquals('glq', $url->glq());
		$this->assertEquals('foobar', $url->res());
		$this->assertEquals('query', $url->query());
	}
	
	public function testUrlNoGtn() {
		$url = new \SpringDvs\Url("spring://a.b.c");
		$this->assertEquals(3, count($url->route()));
		$this->assertEquals('', $url->gtn());
		$this->assertEquals('a', $url->route()[0]);
		$this->assertEquals('b', $url->route()[1]);
		$this->assertEquals('c', $url->route()[2]);
	}
	
	public function testUrlPopping() {
		$url = new \SpringDvs\Url("spring://a.b.c.uk");
		$this->assertEquals(4, count($url->route()));
		array_pop($url->route());
		$this->assertEquals(3, count($url->route()));
	}
	
	public function testUrlToString() {
		$str = "spring://a.b.c.uk:glq/foobar?query";
		$url = new \SpringDvs\Url($str);
		
		$this->assertEquals($str, $url->toString());
	}
}
