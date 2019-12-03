# Laravel Route From Model

[![Build Status](https://travis-ci.com/TomHart/laravel-route-from-model.svg?branch=master)](https://travis-ci.com/TomHart/laravel-route-from-model)

This allows a route to be dynamically built just from a Model instance.

Imagine a route called `test`:
     
     '/test/{name}/{id}'
Calling:

     route_from_model('test', Site::find(8));
will successfully build the route, as `name` and `id` are both attributes on the Site model.

Further more, once using `route_from_model`, the route can be changed. Without changing the call:
     
     route_from_model('test', Site::find(8));
You can change the route to be:
     
     '/test/{name}/{id}/{parent->relationship->value}/{slug}/{otherParent->value}'
And the route will successfully change, as all the extra parts can be extracted from the Model.

Relationships can be called and/or chained with "->" (Imagine Model is a Order):

     {customer->address->postcode}
Would get the postcode of the customer who owns the order.

You can also add the `BuildRouteTrait` to your model, and providing the model has a 

    private $routeName = 'abc';
    
property, you can build a route using:

    $route = $model->buildRoute();