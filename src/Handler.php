<?php

namespace sndsgd\event;


/**
 * An event handler
 */
class Handler extends Base
{
   /**
    * The event handler function
    *
    * @var callable
    */
   protected $handler;

   /**
    * Create a new event handler
    *
    * @param string $event The type/namespace combo for an event
    */
   public function __construct($event, callable $handler)
   {
      list($this->type, $this->namespace) = Event::split($event);
      $this->handler = $handler;
   }

   /**
    * Determine if the handler can handle an event type and namespace
    *
    * @param string|null $type An event type
    * @param string|null $namespace An event namespace
    * @return boolean
    */
   public function canHandle($type, $namespace)
   {
      # no type; namespace must match
      if ($type === null) {
         if ($this->namespace === null || $namespace !== $this->namespace) {
            return false;
         }
      }
      else {
         if ($this->type !== $type) {
            return false;
         }
         else if ($namespace !== null && $this->namespace !== $namespace) {
            return false;
         }
         else if ($namespace === null && $this->namespace !== null) {
            return false;
         }
      }

      return true;
   }

   /**
    * Call the handler function
    *
    * @return boolean
    */
   public function handle(Event $event)
   {
      return call_user_func($this->handler, $event);
   }
}

