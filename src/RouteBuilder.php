<?php

namespace TomHart\Routing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use TomHart\Utilities\ArrayUtil;

class RouteBuilder
{

    public function __construct()
    {
    }

    /**
     * Get the router instance.
     *
     * @return Router
     */
    private function getRouter(): Router
    {
        return app('router');
    }

    /**
     * Get the UrlGenerator.
     *
     * @return UrlGenerator
     */
    private function getUrlGenerator(): UrlGenerator
    {
        return app('url');
    }

    /**
     * This allows a route to be dynamically built just from a Model instance.
     * Imagine a route called "test":
     *      '/test/{name}/{id}'
     * Calling:
     *      routeFromModel('test', Site::find(8));
     * will successfully build the route, as "name" and "id" are both attributes on the Site model.
     *
     * Further more, once using routeFromModel, the route can be changed. Without changing the call:
     *      routeFromModel('test', Site::find(8));
     * You can change the route to be:
     *      '/test/{name}/{id}/{parent->relationship->value}/{slug}/{otherParent->value}'
     * And the route will successfully change, as all the extra parts can be extracted from the Model.
     * Relationships can be called and/or chained with "->" (Imagine Model is a Order):
     *      {customer->address->postcode}
     * Would get the postcode of the customer who owns the order.
     *
     * @param string $routeName The route you want to build
     * @param Model $model The model to pull the data from
     * @param mixed[] $data Data to build into the route when it doesn't exist on the model
     *
     * @return string The built URL.
     */
    public function routeFromModel(string $routeName, Model $model, array $data = [])
    {
        $router = $this->getRouter();
        $urlGen = $this->getUrlGenerator();
        $route = $router->getRoutes()->getByName($routeName);

        if (!$route) {
            throw new RouteNotFoundException("Route $routeName not found");
        }

        $params = $route->parameterNames();

        $data = ArrayUtil::populateArrayFromObject($params, $model, $data);

        return rtrim($urlGen->route($routeName, $data), '?');
    }
}
