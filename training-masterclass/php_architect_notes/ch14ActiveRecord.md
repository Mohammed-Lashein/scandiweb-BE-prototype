# Here I write notes about new things I learnt from this chapter

**Note 1** : fetchAll() and describe statement
```
$desc = $pdo->query("DESCRIBE bookmark")->fetchAll(PDO::FETCH_COLUMN);
```

It is just a single line, but has a lot of new things to learn . 

1. What is DESCRIBE ?
  - It is a sql statement 
  - shorthand for using "SHOW COLUMNS FROM"

2. What is the difference between sql statement and a clause ?
=> A clause is a part of a statement , example
 "select * from bookmark -- select clause
 where url like "%.com% -- where clause
 " " 

3. Why didn't we call DESCRIBE as a clause ?
=> Because : 
- A clause must be a part of a statement 
- It is not a modifying part of another statement—-it is a complete instruction on its own.

4. What about fetchAll(PDO::FETCH_COLUMN) ?
It will be better to try and dump values instead of reading my
explanations . 
Try : 
- fetchAll()
- fetchAll(PDO::FETCH_GROUP)
- fetchAll(PDO::FETCH_COLUMN)
- fetchAll(PDO::FETCH_COLUMN, 0)

My explanations to clarify what each method returns : 

If we called fetchAll() on the returned result of "describe
bookmark" then we will get a bunch of stuff (try logging to
see them) .

But if we call it fetchAll(PDO::FETCH_GROUP), we will get
each column name as the key then it will have a value of the
rest of the properties . 

fetchAll(PDO::FETCH_COLUMN) : will return an array containing
values from the first column returned by our query 'describe
bookmark' which is the Field column (we haven't created this
column, it is returned by default by sql on running the
describe statement)

___
**Note 2** I don't understand what this query returns : 
```
$count = $pdo->query('select count(*) from bookmark')->fetchColumn()
```

Let's tackle it step by step : 
- If you run the below code 
```
var_dump($pdo->query('select count(*) from bookmark')->fetchAll())
```
You will get ```['count(*)' => 0]```

So we are simply using fetchColumn() to get directly the value of
the column in the row from the fetched result set 

___
**Note 3** first time to encounter the error "Constant expression
contains invalid operations"

Although a misleading error, I thought is was due to storing a
sql statement in a string (which is totally non-sense) but after
reading and carefully inspecting the line causing the error it
seems that is was this line 
```
private $pdo = Container::get('ActiveRecordLearningDBPDOConn');
``` 

The reason of the error : The line
```Container::get('ActiveRecordLearningDBPDOConn')``` can't be
evaluated at compile time (it will be evaluated at runtime) and
you can't assign values to constants or class properties like
that .

I also found [this answer on stack
overflow](https://stackoverflow.com/questions/40171546/php-error-fatal-error-constant-expression-contains-invalid-operations)

___
**Note 4**
Taken this test : 
```
test("create multiple bookmark links", function() {
  $link1 = new Bookmark;
  $link1->url = 'https://yahoo.com';
  $link1->name = 'yahoo';
  $link1->description = 'yahoo link1';
  $link1->tag = 'like facebook';

  $link1->save();
  expect($link1->getId())->toEqual(1);
  
  $link2 = new Bookmark;
  $link2->url = 'https://facebook.com';
  $link2->name = 'facebook';
  $link2->description = 'facebook link2';
  $link2->tag = 'fb made react and gql';

  $link2->save();
  expect($link2->getId())->toEqual(2);

  /* 
      expect($link1->getId())->toEqual(1);
      expect($link2->getId())->toEqual(2);
  */
});
```

At first, I placed the expectations in the commented way, the
test failed with the error saying 'Failed asserting that '2'
matches expected 1.' at it was pointing to the 1st expect !

This was mentioned in php docs regarding PDO::lastInsertId(), where
it returns the latest value of the auto_increment as a result of
the most recently executed INSERT statement.

This info is mentioned
[in a comment in php
docs](https://www.php.net/manual/en/pdo.lastinsertid.php#122009)
___
**Note 5** : Taken this code : 
```
public function create($attributes) {
    // create new instance
    $instance = new static;
    // get intersecting keys
    foreach($attributes as $key => $value) {
      if(in_array($key, $instance->fillable)) {
        // foreach key assign its value to the corresponding obj property
        $instance->$key = $value;
      }
    }
    // call save method
    $instance->save();
    return $instance;
  }
```
How are we accessing ```$instance->fillable``` even though
fillable property is protected so can't be accessed from an
instance ?

=> Since we are still in the class body, we can access that
property . It will be inaccessible if we tried to access it
outside of the class . 

___
In the test having the label "Find bookmarks by description", I
wondered how on dumping info about the result of the
findByDescription() the id in the returned elements is null . 

After carefully looking at the code, I found the issue . 
I looped over the query result to return each array into an
instance of the Bookmark class .
And since the id property is not present in the fillable array,
it didn't get a value from the query result . 

Then how does the test ```expect($res[0]->getId())->toBe(1);```
not fail ?
=> Simply because in the getId() implementation, we are querying
the db for the element id based on the url (which is expected to
be specific for each instance) thus we get the correct id . 

It is a bit weird functionality, but since this chapter focuses
more on testing and learning Active Record pattern, I won't
bother with these side issues (Maybe you can try tackling it as a
challenge !) 

