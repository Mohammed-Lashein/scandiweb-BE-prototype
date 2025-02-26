<?php

use App\Http\Controllers\GraphQLController;
use Core\Router;

Router::get('/graphql', [GraphQLController::class, 'index']);
/* Mahmoud implemented get and post, but most
- if not all - gql requests are of post type . 

So I will delete the get and just use the post method 
*/
Router::post('/graphql', [GraphQLController::class, 'index']);