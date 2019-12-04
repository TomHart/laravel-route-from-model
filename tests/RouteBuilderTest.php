<?php

namespace TomHart\Routing\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use InvalidArgumentException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use TomHart\Routing\RouteBuilder;
use TomHart\Routing\Traits\BuildRouteTrait;

class RouteBuilderTest extends TestCase
{

    /**
     * Create a UrlGenerator from the routes, bind it to the container, and
     * create and return a new instance of RouteBuilder.
     * @param Router $router
     * @return RouteBuilder
     */
    private function bindUrlGenAndGetRouteBuilder(Router $router): RouteBuilder
    {
        $urlGenerator = new UrlGenerator(
            $router->getRoutes(),
            $request = Request::create('http://www.foo.com/')
        );

        app()->bind('url', function () use ($urlGenerator) {
            return $urlGenerator;
        });

        return app(RouteBuilder::class);
    }


    /**
     * Test that a route can be build from a model instance.
     */
    public function testGetBuildingRouteFromModel(): void
    {
        /** @var Router $router */
        $router = app('router');
        $route = new Route(['GET'], 'foo/{name}', function () {
            return true;
        });
        $route->name('route');
        $router->getRoutes()->add($route);
        $model = new ModelTest();
        $model->name = 'test';

        $builder = $this->bindUrlGenAndGetRouteBuilder($router);
        $this->assertSame('http://www.foo.com/foo/test', $builder->routeFromModel('route', $model));
    }


    /**
     * Test exception thrown with no route.
     */
    public function testNoRoute(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $builder = new RouteBuilder();
        $this->assertSame('http://www.foo.com/foo/test', $builder->routeFromModel('route', new ModelTest()));
    }


    /**
     * Test that a route can be build from a model instance via the helper.
     */
    public function testGetBuildingRouteFromModelViaHelper(): void
    {
        /** @var Router $router */
        $router = app('router');
        $route = new Route(['GET'], 'foo/{name}', function () {
            return true;
        });
        $route->name('route');
        $router->getRoutes()->add($route);
        $model = new ModelTest();
        $model->name = 'test';

        $this->bindUrlGenAndGetRouteBuilder($router);
        $this->assertSame('http://www.foo.com/foo/test', route_from_model('route', $model));
    }

    /**
     * Test a route can be built from a model with a relationship.
     */
    public function testGetBuildingRouteFromModelWithRelationship(): void
    {
        /** @var Router $router */
        $router = app('router');
        $route = new Route(['GET'], 'foo/{name}/{child->name}', function () {
            return true;
        });
        $route->name('route');
        $router->getRoutes()->add($route);
        $model = new ModelTest();
        $model->name = 'test';
        $modelChild = new ModelChildTest();
        $modelChild->name = 'test-child';
        $model->setRelation('child', $modelChild);

        $builder = $this->bindUrlGenAndGetRouteBuilder($router);
        $this->assertSame('http://www.foo.com/foo/test/test-child', $builder->routeFromModel('route', $model));
    }

    /**
     * Test you can pass extra data along with a model.
     */
    public function testGetBuildingRouteFromModelAndStaticData(): void
    {
        /** @var Router $router */
        $router = app('router');
        $route = new Route(['GET'], 'foo/{name}/{child->name}/{extra}', function () {
            return true;
        });
        $route->name('route');
        $router->getRoutes()->add($route);
        $model = new ModelTest();
        $model->name = 'test';
        $modelChild = new ModelChildTest();
        $modelChild->name = 'test-child';
        $model->setRelation('child', $modelChild);

        $builder = $this->bindUrlGenAndGetRouteBuilder($router);
        $this->assertSame(
            'http://www.foo.com/foo/test/test-child/something',
            $builder->routeFromModel('route', $model, [
                'extra' => 'something',
            ])
        );
    }


    /**
     * Test that a route can be build from a model instance via the trait.
     */
    public function testGetBuildingRouteFromModelViaTrait(): void
    {
        /** @var Router $router */
        $router = app('router');
        $route = new Route(['GET'], 'foo/{name}', function () {
            return true;
        });
        $route->name('route');
        $router->getRoutes()->add($route);

        $model = new ModelTraitTest();
        $model->name = 'test';

        $this->bindUrlGenAndGetRouteBuilder($router);
        $this->assertSame('http://www.foo.com/foo/test', $model->buildRoute());
    }

    /**
     * Test exception throw when using trait with no routeName property.
     */
    public function testExceptionThrowIfNoRouteName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $model = new ModelTraitNoRouteNameTest();
        $model->buildRoute();
    }
}

/**
 * @property string $name
 */
class ModelTest extends Model
{
    public function child(): HasOne
    {
        return $this->hasOne(ModelChildTest::class);
    }
}

/**
 * @property string $name
 */
class ModelChildTest extends Model
{
}

/**
 * @property string $name
 */
class ModelTraitTest extends Model
{
    use BuildRouteTrait;

    /** @var string */
    private $routeName = 'route';
}

/**
 * @property string $name
 */
class ModelTraitNoRouteNameTest extends Model
{
    use BuildRouteTrait;
}