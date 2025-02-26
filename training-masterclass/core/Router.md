# Router notes

- In Pro php8 mvc book, the writer dedicated a class called Route and it
was a really good separation of concern . 
However, I see that using a property in the Router class to store our
routes will be more than enough for this task (Don't complicate things
on yourself ) . 

- Since we are not getting any inputs from the user, I will postpone adding csrf protection . 
- No need to trim the / from the path since $_SERVER['REQUEST_URI'] returns by default the path prefixed with a slash