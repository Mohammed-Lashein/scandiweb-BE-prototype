**Question 1:** Where will a config obj get instantiated from as I don't see any new Config() in the repo ?

=> It will be instantiated in the get() method, ~~and that instance will be stored in a static property in the class~~ . 

There is no need to store the instance because obj instantiation from the Config class is for merely filling the $config static property with data . 
______
**Note 1:**  
In the below code : 

```
 if (is_dir(CONFIG_PATH . $config)) {
    continue;
  }
```
It is IMPORTANT to use CONFIG_PATH constant, because without it the 
Config class can't be tested since that in is_dir documentation :  If filename is a relative filename, it will be checked relative to the current working directory (and this will cause the path to be relative to tests/Unit/core dir which absolutely doesn't have the config dir).
_____
**Note 2:** 
Regarding Config::get($key), I did not want to reinvent the wheel, so I 
decided to inspect laravel source code . 

After asking chat, he told me that the code for Config class exists in
```vendor/laravel/framework/src/Illuminate/Config/Repository.php``` 
(The filename is not Config.php as I thought ! This is expected from
laravel where they are naming the class as Config but actually returning a Repository class thanks to service containers) . 

I inspected the code and the main implementation was by Arr::get($array, $key, $default) method . 

Chat suggested a more explicit implementation than the one present in laravel source code . It was better because it used plain php neither laravel Arr collection nor accessing some static laravel specific methods .

The implementation in the code is what chat suggested, but I couldn't get my mind about one thing : 

**How are we returning array variable at the end even though we want the value from the configuration array?**  

=> If you look carefully, debug using xdebug (which I highly recommend you take some time (and some frustration) to install on your machine) or even debug using a paper , you will notice that we are actually traversing the static::config (**I couldn't add dollar sign because it makes the markdown be written in a cursive font**) array recursively till we find a match for our requested config info, like 'database.host' . 

If we were not able to find that config info, the default will be returned (which will usually be null) . 
_____

I tried to write the code on my own, and it worked, but it was not scalable . 
In other words, if the config data we were searching for was one extra level deep, the code would have failed . 

So using laravel approach is definitely much better .

My first try code (just for documentation) : 

```
  public static function get($key, $default = null){
    if(count(static::$config) === 0) {
      static::requireConfigData();
    }
    [$configKey, $value] = explode(".", $key);
    return static::$config[$configKey][$value];
  }

```
_____

A grammatical note (will definitely help in writing correct commit messages) : 
It is (will usually be) not (will be usually) because word order rule is:
1. Auxiliary verb (e.g., "will")
2. Adverb (e.g., "usually")
3. Main verb (e.g., "be")

_____
Last note regarding Config class : 

I used config_entries var name instead of config_files as we want our method to work even if we had dirs in the config dir (which is a bit unusual but just in case) . This name was suggested along other variable names from chat and I chose this one . 