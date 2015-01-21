<?php

namespace sndsgd\event;

use \InvalidArgumentException;
use \sndsgd\Event;


/**
 * A trait that allows objects to receive events
 */
trait Target
{
   /**
    * Handler functions, keyed by event type
    * 
    * @var array.<string, sndsgd\event\Handler>
    */
   protected $eventHandlers = [];

   /**
    * Get all event handlers
    *
    * @return array.<sndsgd\event\Handler>
    */
   public function getEventHandlers()
   {
      return $this->eventHandlers;
   }

   /**
    * Add an event handler
    * 
    * @param string $event The name of the event
    * @param string|callable $handler The function that handles the event
    * @param boolean $prepend Add the handler at the top of the stack
    * @return sndsgd\event\Target
    * @throws InvalidArgumentException If the event isn't a string
    */
   public function on($event, $handler, $prepend = false)
   {
      if (!is_string($event)) {
         throw new InvalidArgumentException(
            "invalid value provided for 'event'; ".
            "expecting an event name as string"
         );
      }

      $handler = new Handler($event, $handler);
      if ($prepend === false) {
         $this->eventHandlers[] = $handler;
      }
      else {
         array_unshift($this->eventHandlers, $handler);
      }

      return $this;
   }

   /**
    * Remove one or more event handlers
    *
    * @param string $event An event type/namespace combo
    * @return sndsgd\event\Target
    */
   public function off($event)
   {
      list($type, $namespace) = Event::split($event);

      for ($i=count($this->eventHandlers)-1; $i>-1; $i--) {
         $h = $this->eventHandlers[$i];
         if ($type !== null && $h->getType() !== $type) {
            continue;
         }
         else if ($namespace !== null && $h->getNamespace() !== $namespace) {
            continue;
         }
         
         array_splice($this->eventHandlers, $i, 1);
      }

      return $this;
   }

   /**
    * Call all handlers for a given event
    *
    * Note: if a handler returns boolean false, any remaining handlers are skipped
    * @param sndsgd\Event|string $event An event, or a type/namespace combo
    * @param array.<string,mixed> $data Data to add to the event
    * @return boolean
    * @return boolean:false A handler returned false
    * @return boolean:true All handlers returned true or no handlers exist
    * @throws InvalidArgumentException If the event isn't a string
    */
   public function fire($event, array $data = [])
   {
      if (is_string($event)) {
         $event = new Event($event);
         $event->setData($data);
      }
      else if ($event instanceof Event) {
         if ($data) {
            $event->addData($data);
         }
      }
      else {
         throw new InvalidArgumentException(
            "invalid value provided for 'event'; ".
            "expecting an event name as string"
         );
      }

      $type = $event->getType();
      $namespace = $event->getNamespace();
      foreach ($this->eventHandlers as $handler) {
         if (
            $handler->canHandle($type, $namespace) && 
            $handler->handle($event) === false
         ) {
            return false;
         }
      }
      return true;
   }
}

