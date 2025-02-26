# Here I document the notes regarding the repo folder structure

An important note => Instead of cluttering the source files with comments, I will write a separate md file for my notes about a file or a class . 

Example : Database.php will have a corresponding file called Database.md that has all the notes about that php file 

1. **Most of the directory structure follows laravel conventions**
2. However, a core directory is used in laracasts php course and not 
by laravel specifically . 

The instructor in that course explained that the classes that correspond
to the core logic of our app will reside in the core directory

3. What is the need of the public folder ?
=> It is the entry point of our app . 
Our service container is registered there and also router initialization . 

4. Why do we need to register the router in the public dir instead of 
just manually navigating the project directories and files ?
=> Having a router is much better as it has some advantages like : 

  a. Not exposing our project structure on the client side
  b. We can change the files structure without affecting the routes