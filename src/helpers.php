<?php

use Illuminate\Database\Eloquent\Model;
use TomHart\Routing\RouteBuilder;

if (!function_exists('route_from_model')) {
    /**
     * This allows a route to be dynamically built just from a Model instance.
     * Imagine a route called "test":
     *      '/test/{name}/{id}'
     * Calling:
     *      route_from_model('test', Site::find(8));
     * will successfully build the route, as "name" and "id" are both attributes on the Site model.
     *
     * Further more, once using "route_from_model", the route can be changed. Without changing the call:
     *      route_from_model('test', Site::find(8));
     * You can change the route to be:
     *      '/test/{name}/{id}/{parent->relationship->value}/{slug}/{otherParent->value}'
     * And the route will successfully change, as all the extra parts can be extracted from the Model.
     * Relationships can be called and/or chained with "->" (Imagine Model is a Order):
     *      {customer->address->postcode}
     * Would get the postcode of the customer who owns the order.
     * @param string $routeName The route you want to use
     * @param Model $model
     * @param array $data
     * @return string
     */
    function route_from_model(string $routeName, Model $model, array $data = [])
    {
        return app(RouteBuilder::class)->routeFromModel($routeName, $model, $data);
    }
}
