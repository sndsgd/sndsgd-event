<?php

use \sndsgd\Event;


class EventTest extends PHPUnit_Framework_TestCase
{
   public function testSplit()
   {
      $this->assertEquals(['type', null], Event::split('type'));
      $this->assertEquals(['type', null], Event::split('type.'));
      $this->assertEquals(['type', 'ns'], Event::split('type.ns'));
      $this->assertEquals([null, 'ns'], Event::split('.ns'));
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testSplitException()
   {
      Event::split(123);
   }

   public function test()
   {
      $ev = new Event('parse');
      $this->assertEquals('parse', $ev->getType());
      $this->assertNull($ev->getNamespace());

      $ev = new Event('parse.ns');
      $this->assertEquals('parse', $ev->getType());
      $this->assertEquals('ns', $ev->getNamespace());

      $ev = new Event('.ns');
      $this->assertNull($ev->getType());
      $this->assertEquals('ns', $ev->getNamespace());
   }

}

