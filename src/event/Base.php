<?php

namespace sndsgd\event;


abstract class Base
{
   /**
    * The event type
    *
    * @var string|null
    */
   protected $type = null;

   /**
    * The event namespace
    *
    * @var string|null
    */
   protected $namespace = null;

   /**
    * Get the event type
    *
    * @return string|null
    */
   public function getType()
   {
      return $this->type;
   }

   /**
    * Get the event namespace
    *
    * @return string|null
    */
   public function getNamespace()
   {
      return $this->namespace;
   }
}

