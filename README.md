# Laravel Route From Model

[![Build Status](https://travis-ci.com/TomHart/laravel-route-from-model.svg?branch=master)](https://travis-ci.com/TomHart/laravel-route-from-model)
[![codecov](https://codecov.io/gh/TomHart/laravel-route-from-model/branch/master/graph/badge.svg)](https://codecov.io/gh/TomHart/laravel-route-from-model)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TomHart/laravel-route-from-model/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TomHart/laravel-route-from-model/?branch=master)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/TomHart/laravel-route-from-model?color=green)
![GitHub](https://img.shields.io/github/license/TomHart/laravel-route-from-model?color=green)

This library allows a route to be built just from a `Model` instance, automatically pulling out the parameters, rather
than having to manually pass them.


### Simple Example
Imagine a route called `test`:
     
```php
Route::get('/test/{name}')->name('test');
```
Calling:

```php
route_from_model('test', SomeModel::find(1));
```
will successfully build the route, as `name` is an attribute on `SomeModel` that can be retrieved.

Now imagine you want to change the route to be:

```php
Route::get('/test/{name}/id/{id}/{seo_slug}')->name('test');
```

Using the default route building in Laravel, you'd need to manually go to everywhere the route
is built, and specify what/where the extra `id` and `seo_slug` data should come from. Providing they
exist on `SomeModel`, using the exact same `route_from_model` call above, it will automatically be able
to build the route without you needing to change anything. 

### Relationship

Using `route_from_model`, you're also able to automatically get data from model relationships too, by using `->`

```php
Route::get('/test/{name}/{id}/{parent->relationship->value}/{slug}/{child->value}')->name('test');
```
Providing all those relationships/attributes exist, `route_from_model` will be able to build the URL.
And the route will successfully change, as all the extra parts can be extracted from the `Model`.

### Trait
You can also add the `BuildRouteTrait` to your model, and providing the model has a 

```php
private $routeName = 'test';
```    
property, you can build a route using:

```php
$route = $model->buildRoute();
```

### Attributes and static values
You can also combine `route_from_model` with static values too. Imagine the route: 

```php
Route::get('/test/{name}/{static}')->name('test');
```
where static **isn't** an attribute available on `SomeModel`, you can simply pass it an array as the third parameter.

```php
route_from_model('test', SomeModel::find(1), ['static' => 'MyValue']);
```