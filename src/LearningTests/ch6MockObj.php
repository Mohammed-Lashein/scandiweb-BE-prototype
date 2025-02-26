<?php

class Temperature {
  /**
   * @var Mockery $service
   */
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function average()
    {
        $total = 0;
        for ($i=0; $i<3; $i++) {
            $total += $this->service->readTemp();
        }
        return $total/3;
    }
}


class Accumulator {
    private $total = 0;
    public function add($amount) {
      $this->total += $amount;
    }
    public function getTotal() {
      return $this->total;
    }
}

function calc_total($items, $acc) {
  foreach($items as $item) {
    $acc->add($item);
  }
}
function calc_tax($acc, $rate = 0.07) {
  return (int) round($acc->getTotal() * $rate, 2);
}

// ===== Legacy form stuff =====
class Session {
  /* Why do we need this class ?
  In the legacy form page, we are using session super global . 
  This won't be available to us in testing , why ?
  => Bec if any change occurs to the session will affect our app . So the best thing to do is to wrap it in a wrapper . 
  
  TODO : I still don't understand why using a wrapper will be any better
  than just modifying the session directly . 

  Also in the wrapper Imp we are not copying the superglobal but instead
  modifying it directly . 
  */
  public function __construct() {
    static::init();
  }
  private static function init() {
    if(!isset($_SESSION)) {
      if(headers_sent()) {
        trigger_error("Session has not started before creating session obj");

        /*
        What does that above error mean ?
        => In the conditional, we see
            - If the $_SESSION is not set
            - But the headers have been sent
            - Then we can't access session data through this wrapper but
            we should through an error 
         */
      } else {
        session_start();
      }
    }
  }
  public function isValid($key) {
    return array_key_exists($key, $_SESSION);
  }
  public function get($key) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
  }
  public function set($key, $value) {
    return $_SESSION[$key] = $value;
  }
  public function clear($key) {
    unset($_SESSION[$key]);
  }
}


/* 
  The writer called it UserLogin, but it is actually an admin login . 
  NO : It is correct to name it UserLogin . Since this is a server stub,
  it is expected to behave the way we want, but only in a limited set of
  circumstances . 
*/
class UserLogin {
  private $id;
  private $name;
  private $valid = true;

  public function __construct($name) {
    switch(strtolower($name)) {
      case 'admin':
        $this->name = $name;
        $this->id = 1;
        break;
      default:
      trigger_error("Name : $name is not auth");
      $this->valid = false;
    }
  }
  public function getName() {
    if($this->valid) {
      return $this->name;
    }
    return null;
  }
  public function validate($user_name, $password) {
    if($user_name === 'admin' && $password === 'secret') {
      return true;
    }
    return false;
  }
}

class Response {
  private $head;
  private $body;
  public function addHead($content) {
    $this->head .= $content;
  }
  public function addBody($content) {
    $this->body .= $content;
  }
  public function display() {
    echo $this->fetch();
  }
  public function fetch() {
//     return <<<nono
//      <html>
//       <head> $this->head </head>
//       <body> $this->body </body>
//     </html>
//     nono;
// ;
    return "<html><head>$this->head</head><body>$this->body</body></html>";
  }
  public function redirect($to, $exit = false){
    header("Location: $to");
    if($exit) exit;
  }
}

class PageDirector {
  private $session;
  private $res;
  public function __construct($s, $r) {
    $this->session = $s;
    $this->res = $r;
  }
  public function run() {
    if(!$this->isLoggedIn()) {
      return $this->showLogin();
    }
    return $this->res->display();
  }
  private function isLoggedIn() {
    return $this->session->get(["user_name"]) ? true : false;
  }
  private function showLogin() {
    $this->res->addBody("<form method='post'>");
    $this->res->addBody("<input type='text' name='name'/>");
    $this->res->addBody("\n");
    $this->res->addBody("<input type='password' name='passwd'/>");
    $this->res->addBody("\n");
    $this->res->addBody("<input type='submit' value ='Login'/>");
    $this->res->addBody("</form>");
  }
  public function runPage($page) {
    ob_start();
    $page->run();
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
}
// $page = new PageDirector(new Session, new Response);
// $page->run();