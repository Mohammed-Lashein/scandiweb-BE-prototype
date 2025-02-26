### The Database class follows the singleton pattern . 
Without studying that pattern from php objects, patterns and practice book (I will refer to it as popps, as the writer did that in the book) most of the code in this class won't make sense unless you study that part in the book . 

### Questions

**Question 1:**  Why did we use a private constructor ?
=> To open just one connection to the db across our entire app . 

What is the advantage of doing this ?
=> We are saving memory (but preventing open multiple connections to the db), and also applying the singleton pattern . 

**Question 2:** I see the Database class cluttered with *static* and *self* . What is the difference between both ?

=> In popps book, the writer explained the difference in great detail . But in a nutshell : 
1. self returns the parent class
2. static returns the class calling the method (so if the method were defined in the parent class and a child class called it, static will resolve to the child class (the calling class) )

In our case, there is no inheritance, so the Database class is the one calling the method, so self and static will behave in the same way . 

NOTE : there is a difference between : 
return new static;   AND public static function getInstance() 

The static before a method definition can't be written as self, because that static keyword is specific for OOP

**Question 3:** Why did we name the method getInstance() instead of instance() ?
=> That's the way Eng Martin Fowler used it in his book -- Patterns of Enterprise Application Architecture . 
Also in PHP architect book, the writer followed that rule . 

**Note:** Here we are implementing the Singleton pattern, while in the *Container class* we are implementing the Registry pattern . 

Each pattern definition is really obvious in php architect book, so checking it out is great for understanding . 