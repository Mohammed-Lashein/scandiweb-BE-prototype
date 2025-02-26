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

I found [this answer on stack overflow](https://stackoverflow.com/questions/40171546/php-error-fatal-error-constant-expression-contains-invalid-operations)