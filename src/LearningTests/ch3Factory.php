<?php

require_once "ch2.php";

// There is no need to call it ColorFactory since it will be obvious
// when we read the code that this is a factory
class Color {
  private $r;
  private $g;
  private $b;
  public function __construct($r= 0, $g = 0, $b = 0) {
    $this->r = $this->validateColor($r);
    $this->g = $this->validateColor($g);
    $this->b = $this->validateColor($b);

  }
  public function validateColor($color) {
    $check = (int)$color;
    if($check < 0 || $check > 255) {
      throw new InvalidArgumentException("The color: $color is out of bounds 0 and 255");
    } else {
      return $color;
    }

  }
  /**
   * returns the hexcode of a color
   * @return string  hexcode of a color
   */
  public function getRgb() {
    return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b);

    /* explanation of the above line : 
    sprintf : returns a formatted string
    # : will be added to the returned string
    % : determines that this is the beginning of a format specifier
    x : return the value in lowercased hex format
    02 : min 2 nums returned, if not then pad with 0 
     */
  }
}
class CrayonBox {
  public function colorList() {
    return [
      'red' => [255,0,0],
      'lime' => [0,255,0],
    ];
  }
  public function getColor($color_name) {
    $colors = $this->colorList();
    $color_name = strtolower($color_name);
    if(array_key_exists($color_name, $colors)) {
      return new Color(...$colors[$color_name]);
    } 
    // throw new Error("$color_name is an invalid color ");
    /*
    Now I get it : On triggering an err, the tests will return to us
    a notice instead of a failing test . 
    
    This is much better than throwing an exception as it allows the app
    to exit gracefully . 

    Note that it is also amazing that pest returns the filename along
    with the line containing the error . What I received : 
    // src/LearningTests/ch3.php:61

    (NOTE : we would get that if we used --display-errors flag)
    */
    trigger_error("$color_name is an invalid color");
    return new Color;
  }
}

// Monopoly advanced stuff 

/* 
  Some business logic explained : 
  [1] When you purchase a property, you get a deed . 
    what is a deed ?
    => num of basic facts about the property that are used throughout 
    the gameplay . 

  [2] There are 3 types of properties
    [1] Streets
    [2] Railroads
    [3] Utils

  [3] Every property has some chchs :
    [1] can be owned by a player
    [2] has a price
    [3] generates rent for its owner whenever other players land on it

    [4] But some aspects of each kind of real estate are very different. For example, the formula for calculating rent depends on the type of property.

*/

abstract class Property {
  protected $name;
  protected $price;
  // protected Monopoly $game;
  protected $game;
  protected $owner;

  public function __construct($n,$p,$g) {
    $this->name = $n;
    $this->price = new GoodDollar($p);
    $this->game = $g;
  }
  abstract protected function calcRent();

  public function purchase(Player $player) {
    $player->pay($this->price);
    $this->owner = $player;
  }
  public function collectRentFrom($player) {
    // This property is owned by a player
    if($this->owner) {
      // AND The player passing is not the owner
      if($this->owner != $player) {
        /*
        The commented code is the implementation in the book . But 
        since we implemented payRent before in the Monopoly class, it 
        makes sense to reuse the logic instead of creating a new one . */
        // $this->owner->collect($player->pay($this->calcRent()));

        $this->game->payRent($player, $this->owner, $this->calcRent());
      }
    }
  }
}

class Street extends Property {
  protected $base_rent;
  public $color;

  public function setRent($rent) {
    $this->base_rent = new GoodDollar($rent);
  }

  public function calcRent() {

    /* IMPORTANT : 
      - hasMonopoly() method is not implemented 
      - However, its idea is important : 
        In the game, if a player has all properties having the same color, the rent doubles . 
        Otherwise, the base rent is returned . 

        I needn't try to understand chat suggestion to that method
        logic, I am just adding it here for documentation only

      - Also note that Railroads and Utils don't have colors in the
      game, hence we won't use hasMonopoly() in their calcRent()
     */

    // if($this->game->hasMonopoly($this->owner, $this->color)) {
    //   return $this->base_rent->add($this->base_rent);
    // }

    return $this->base_rent;
  }
}

class RailRoad extends Property {
  protected function calcRent() {
    /* Again there is no need to implement the logic, I will just
    move on as my focus is on learning design patterns and testing, not
    monopoly game */
    // switch($this->game->railRoadCount($this->owner)) {
    switch(round(rand(0,4))) {
      case 1 : return new GoodDollar(25);
      case 2 : return new GoodDollar(50);
      case 3 : return new GoodDollar(100);
      case 4 : return new GoodDollar(200);
      default : return new GoodDollar();
    }
  }
}

class Utility extends Property {
  public function calcRent() {
    switch(round(rand(0,2))) {
      case 1 : return new GoodDollar(4);
      case 2 : return new GoodDollar(10);
      default : return new GoodDollar();
    }
  }
}

// Property factory class
/* What does it mean ?
=> I am not sure, but maybe it means that the Assessor is a factory
that will create Property classes

  TRUE !! 

  The Assessor class will be the factory that is responsible for 
  creating different types of Properties (Objects extending Property class)
 */
class Assessor {
  protected $game;
  protected $prop_info = array(
    // streets
    'Mediterranean Ave.' => array('Street', 60, 'Purple', 2),
    'Baltic Ave.' => array('Street', 60, 'Purple', 2) 
    //more of the streets...
    ,'Boardwalk' => array('Street', 400, 'Blue', 50) 
    // railroads
    ,'Short Line R.R.' => array('RailRoad', 200),
    //the rest of the railroads...
    // utilities
    'Electric Company' => array('Utility', 150) ,
    'Water Works' => array('Utility', 150) 
  );

  public function setGame($game) {$this->game = $game;}
  public function getProperty($name) {
    // $prop_info = new PropertyInfo($this->prop_info[$name]);
    $prop_info = $this->getPropInfo($name);
    switch($prop_info->type) {
      case "Street":
        $prop = new Street($name, $prop_info->price, $this->game);
        $prop->color = $prop_info->color;
        $prop->setRent($prop_info->rent);
        return $prop;
        break;
      case "RailRoad":
        return new RailRoad($name, $prop_info->price, $this->game);
        break;
      case "Utility":
        return new Utility($name, $prop_info->price, $this->game);
        break;
      default;
    }
  }
  protected function getPropInfo($name) {
    if(!array_key_exists($name, $this->prop_info)) {
      throw new InvalidArgumentException("$name is not a property name");
    }

    return new PropertyInfo($this->prop_info[$name]);
  }
  
}
class TestableAssessor extends Assessor {
  public function getPropInfo($name) {
    return Assessor::getPropInfo($name);
    // return parent::getPropInfo($name)

    /* 
      After speaking with chat for some time, he told me that the code
      in the book is deprecated where we can't call a non-static method
      on a class directly . 

      Since TestableAssessor extends Assessor, we can use parent to call
      the method of Assesssor (but we can't write Assessor::getPropInfo($name))
    */
  }
}

class PropertyInfo {
  const TYPE_KEY = 0;
  const PRICE_KEY = 1;
  const COLOR_KEY = 2;
  const RENT_KEY = 3;
  public $type;
  public $color;
  public $price;
  public $rent;

  public function __construct($props) {
    $this->type = $this->propValue($props, 'type', self::TYPE_KEY);
    $this->price = $this->propValue($props, 'price', self::PRICE_KEY);
    $this->color = $this->propValue($props, 'color', self::COLOR_KEY);
    $this->rent = $this->propValue($props, 'rent', self::RENT_KEY);
  }

  protected function propValue($props, $prop, $key) {
      // array_key_exists(0, ["type", 'price'])
      if(array_key_exists($key, $props)) {
        // return $this->type = ["type", 'price'][0];
        return $this->$prop = $props[$key];
    }
  }


  /*
  Below was my first approach for implementing this fn . 
  I thought that ensuring the order of the els was a bit over-engineering so I didn't count for (take into account) it .

  But after looking at the $prop_info, it seems that we need the info
  returned in an expected order . 
  
  So the writer's implementation is the correct one . 
   */
  // protected function propValue($props, $property) {
  //   foreach($props as $prop) {
  //     if($prop == $property) {
  //       $this->$property = $prop;
  //     }
  //   }
  // }
}
