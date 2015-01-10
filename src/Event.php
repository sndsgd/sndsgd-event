<?php

namespace sndsgd;

use \InvalidArgumentException;


/**
 * An event
 */
class Event extends \sndsgd\event\Base
{
   use \sndsgd\util\data\Manager;

   /**
    * Split an event in the type and namespace portions
    *
    * @param string $event The type/namespace combo for an event
    * @return array.<string|null>
    */
   public static function split($event)
   {
      if (!is_string($event)) {
         throw new InvalidArgumentException(
            "invalid value provided for 'event'; expecting an event name ".
            "or event name and namespace as string"
         );
      }

      list($type, $namespace) = array_pad(explode('.', $event, 2), 2, null);
      return [ 
         ($type === '') ? null : $type,
         ($namespace === '') ? null : $namespace
      ];
   }

   /**
    * Create a new event
    *
    * @param string $event The type/namespace combo for an event
    * @param array $data 
    */
   public function __construct($event, array $data = [])
   {
      list($this->type, $this->namespace) = self::split($event);
      $this->setData($data);
   }
}

