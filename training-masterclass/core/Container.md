# Container notes

The container basically implements the idea of IoC container (inversion of control) .

This idea is greatly explained in Laravel up and running book, and I also wrote [an article about it](#)

Resources for learning more about service containers and understanding them :
1. Laracasts php course
2. Laracasts Laravel course (where you will use service containers
directly and indirectly through automatic dependency injection)
3. Laravel up and running book

Also to my surprise (and also yours) after studying in php architect book, I found that this Container (and probably laravel's also) implements the Registry Pattern (That's why learning design patterns is very important)
_____

## Questions I encountered during learning and their answers

**Thought 1** 

In Laracasts php course, on creating a container, it had 2 methods :
1. bind 
2. resolve

However, in the code written here, there are some other methods like :
1. get
2. set 
3. has

**Question 1:** If we have resolve(), then why do we have get() ?

*Answer* 
- get() is compliant to psr-11 standard for container interface, so it 
is just a wrapper around resolve . 

- Although in Laracasts php course the instructor used resolve, in Laravel we should use make() as resolve() is used internally by the framework . 
Note: In our code here we will not be using make()

Then why did the instructor use resolve() ?
=> Maybe he wanted to introduce the learners to a new key concept (which is bind and resolve) and I agree with that point . 

- By the way, the has() method is also for being compliant with psr-11 standard

You can [read more about the standard](https://www.php-fig.org/psr/psr-11/)

**Question 2:** Why do we have set() when we have bind() ?

*Answer*

Draft : ~~bind is enough as I don't see set in neither psr 11 container nor laravel code~~

set() is implemented in Illuminate\Contracts\Repository interface which Config class implements in laravel . 
(Although I searched for 'class Config implements Repository' but didn't find it directly, so I didn't want to dive into laravel code to find where did they do it )

UPDATE : laravel Config class implements Illuminate\Config\Repository which itself is implementing an interface in Illuminate\Contracts\Config\Repository.php . 

**Question 3:** What about the get() method implementation ?

At first, I implemented it like that : 

```
public static function get($id) {
    // check if we have an entry
    if(static::has($id)) {
      $callableAssocWithId = static::$my_entries[$id];
      return $callableAssocWithId();
    } else {
      // if not instantiate it (using resolve)
      return static::resolve($id);
    }
  }
```
However, I found this piece of code in laravel bind() : 
```
if (! $concrete instanceof Closure) {
  if (! is_string($concrete)) {
    throw new TypeError(self::class.'::bind(): Argument #2 
    ($concrete) must be of type Closure|string|null');
  }
  $concrete = $this->getClosure($abstract, $concrete);
}
```
What this code says is that the 2nd arg passed to bind may be a Closure|string|null . 
Yes we are supposing that it will always be a Closure (in our project) but in terms of scalability, what if we needed to add a string instead ?

But remember : In our code, we are expecting to receive a closure (that's why we are calling it after extracting it) so I think the var name used in our previous way is both explicit and easier to understand . 

The 2nd code provided is from laravel so surely they are expecting all use cases, however our code is specific for our app so there is no need to provide more abstractions . 

Take home note : We will keep the var name as it is . 
_____

I followed laravel conventions In (src/Illuminate/Container/Container.php) for both methods and their params naming . 
____

In laravel, get() throws EntryNotFoundException, which implements some other Exceptions to be [psr-11 compliant](https://www.php-fig.org/psr/psr-11/#12-exceptions)

But it will be an overrhead and I will just throw a native php Error . 

______

resolve() notes : 
- At first, I named the param as id, but I was not convinced because 
on reading the code, how are we passing an id to the ReflectionClass ?
(A bit weird) .

And also how will we write new id (very weird actually) . 

But after inspecting laravel code in src/Illuminate/Container/Container.php, I found that they used concrete param name, which is much more explicit and makes sense on passing the param to the ReflectionClass constructor 

Here is the code : 
```
/* Remember our discussion before that make() is just a wrapper for resolve() */
  public function make($abstract, array $parameters = []) {
      return $this->resolve($abstract, $parameters);
    }
```
_____
**Question 4** : What is the need of the resolve() in the service container ? 
=> It prepares the dependencies a class would need in order to perform correctly . 

**Question 5:** But then I would need to use 'use statement' to import the class name then provide it to resolve (actually resolve works internally, we will work with get as a public interface following psr conventions) . So how is using an IoC container better than just requiring the file ?

=> It is true that you will need to use 'use statement' to resolve the class name with namespaces, but the advantage of using an IoC container is that it is responsible for injecting the deps that any class needs to work well . 

Take this great example from chat : 

```
  /* No service container used */
  use App\Services\UserService;
  use App\Repositories\UserRepository;

  $repository = new UserRepository();
  $service = new UserService($repository);

  /* Using service containers */
  $service = Container::resolve(UserService::class);
```
Did you notice how we didn't need to resolve manually any of the deps that UserService needs in order to work properly . 

____


