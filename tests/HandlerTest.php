<?php

use \sndsgd\Event;
use \sndsgd\event\Handler;

class HandlerTest extends PHPUnit_Framework_TestCase
{
   public static $temp = [];

   public static function exampleHandler(Event $ev)
   {
      self::$temp = [
         'type' => $ev->getType(),
         'namespace' => $ev->getNamespace(),
      ];
      return true;
   }

   public function test()
   {
      $handler = new Handler('parse', function(Event $ev) {
         HandlerTest::$temp = $ev->getData();
      });
      $ev = new Event('parse', ['one' => 1]);
      $handler->handle($ev);
      $this->assertEquals(HandlerTest::$temp, ['one' => 1]);

      $ev = new Event('parse', ['two' => 2]);
      $handler->handle($ev);
      $this->assertEquals(HandlerTest::$temp, ['two' => 2]);
   }

   public function testCanHandle()
   {
      $handler = new Handler('parse', 'HandlerTest::exampleHandler');
      $this->assertTrue($handler->canHandle('parse', null));
      $this->assertFalse($handler->canHandle('parse', 'ns'));
      $this->assertFalse($handler->canHandle('nope', null));
      $this->assertFalse($handler->canHandle('nope', 'ns'));

      $handler = new Handler('parse.ns', 'HandlerTest::exampleHandler');
      $this->assertTrue($handler->canHandle('parse', 'ns'));
      $this->assertTrue($handler->canHandle(null, 'ns'));
      $this->assertFalse($handler->canHandle(null, 'nope'));
      $this->assertFalse($handler->canHandle('parse', null));
      $this->assertFalse($handler->canHandle('nope', 'ns'));
   }
}

