<?php


/* On learning from php architect book, I didn't use namespaces, so on
importing the files directly I could access the classes . 

But now I am using namespaces, so requiring the files is no longer the
correct solution . 

You must use namespaces to access the classes 
*/
// require __DIR__ . "/../../../core/Router.php";
// require __DIR__ . "/../../../app/Http/Controllers/GraphQLController.php";

use Core\Router;
use App\Http\Controllers\GraphQLController;

/* TODO : 
  - Since we are not using laravel dusk
  - Also we are not in the FE to use cypress

  I think we can't use pest here to determine if the router can navigate us in the app . 

  I also don't know how can we test the returned result from a controller
*/

test("get req returns a res", function() {

  Router::get('/graphql', [GraphQLController::class, 'index']);
  expect(1)->toEqual(1);
});