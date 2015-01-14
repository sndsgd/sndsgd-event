<?php

namespace sndsgd\event;

use \sndsgd\Event;


class TestTarget
{
   use \sndsgd\event\Target;
}


class TargetTest extends \PHPUnit_Framework_TestCase
{
   public static $temp = [];

   public static function exampleHandler(Event $ev)
   {
      self::$temp = [
         'type' => $ev->getType(),
         'namespace' => $ev->getNamespace(),
         'data' => $ev->getData()
      ];
      return true;
   }

   public function setUp()
   {
      $this->t = new TestTarget();

      $this->t->on('two', '\sndsgd\event\TargetTest::exampleHandler');
      $this->t->on('three', '\sndsgd\event\TargetTest::exampleHandler');
      $this->t->on('one', '\sndsgd\event\TargetTest::exampleHandler', true);
   }

   private function getOrderedHandlerEventTypes()
   {
      $ret = [];
      $handlers = $this->t->getEventHandlers();
      foreach ($handlers as $handler) {
         $ret[] = $handler->getType();
      }
      return $ret;
   }

   public function testOn()
   {
      $this->assertEquals(3, count($this->t->getEventHandlers()));

      $expect = ['one', 'two', 'three'];
      $this->assertEquals($expect, $this->getOrderedHandlerEventTypes());
   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testOnException()
   {
      $this->t->on(42, '\sndsgd\event\TargetTest::exampleHandler');
   }

   public function testOff()
   {
      $this->t->on('four.ns', '\sndsgd\event\TargetTest::exampleHandler');
      $this->assertEquals(4, count($this->t->getEventHandlers()));
      $this->t->off('.ns');
      $this->assertEquals(3, count($this->t->getEventHandlers()));
      $this->t->off('one');
      $this->assertEquals(2, count($this->t->getEventHandlers()));

      $expect = ['two', 'three'];
      $this->assertEquals($expect, $this->getOrderedHandlerEventTypes());
   }

   public function testFire()
   {
      $this->t->fire('one');
      $expect = ['type' => 'one', 'namespace' => null, 'data' => []];
      $this->assertEquals($expect, self::$temp);

      $this->t->on('four.ns', '\sndsgd\event\TargetTest::exampleHandler');
      $this->t->fire('four'); // doesn't have the namespace
      $this->assertEquals($expect, self::$temp);

      $ev = new Event('four.ns');
      $ev->setData(['one' => 1]);

      $this->t->fire($ev, ['two' => 2]);
      $expect = [
         'type' => 'four', 
         'namespace' => 'ns', 
         'data' => [
            'one' => 1,
            'two' => 2
         ]
      ];
      $this->assertEquals($expect, self::$temp);


      $this->t->fire('.ns');
      $expect = ['type' => null, 'namespace' => 'ns', 'data' => []];
      $this->assertEquals($expect, self::$temp);

      $this->t->fire('.no-matches');
      $this->assertEquals($expect, self::$temp);


      $this->t->on('four', function(Event $ev) {
         return false;
      }, true);
      self::$temp = null;
      $this->assertFalse($this->t->fire('four'));
      $this->assertNull(self::$temp);

   }

   /**
    * @expectedException InvalidArgumentException
    */
   public function testFireException()
   {
      $this->t->fire(42);
   } 
}

