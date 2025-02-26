<?php

namespace Core\Exceptions;

class EntryNotFoundException extends \Exception {
  /*
    No need for this $message property as I prefer more context specific either error or exception messages
  */
  // public $message = "The provided entry not found";
}