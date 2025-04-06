<?php

/* 
  !!! Important to refactor !!!
Since you named the namespace here starting with Core, even though we
are in the Core namespace since we are in core dir, you was obliged to 
import it in Container.php using the Core . 

Just name the namespace here as Exceptions and modify every place you imported that Exception
*/
namespace Core\Exceptions;

class EntryNotFoundException extends \Exception {
  /*
    No need for this $message property as I prefer more context specific either error or exception messages
  */
  // public $message = "The provided entry not found";
}