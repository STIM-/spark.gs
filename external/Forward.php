<?php

  use Silex\Application;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\HttpKernelInterface;


  /**
   *
   *
   *
   */
  $app['forward'] = $app->protect (function ($request, $url, $method = 'GET', $attributes = []) use ($app) {

    $files = $request->files->all ();

    foreach ($files as $k => $v) {
      if (! is_object ($v) && ! is_subclass_of ($v, 'UploadedFile'))
        unset ($files[$k]);
    }

    $handler = Request::create ($url, $method, [], $request->cookies->all (), $files, $request->server->all ());

    foreach ($attributes as $k => $v)
      $handler->attributes->set ($k, $v);

    return $app->handle ($handler, HttpKernelInterface::MASTER_REQUEST, false);

  });


  /**
   *
   *
   *
   */
  $app['forward.invalid'] = $app->protect (function ($request, $attributes = [], $option = []) use ($app) {

    $route  = $request->get ('_route');
    $paras  = $request->get ('_route_params');
    $method = 'GET';
    $parse  = explode (':', $route);

    if (isset ($parse[1]))
      $method = array_shift ($parse);

    $route = $parse[0];

    $opt = $option + [
      'route'  => $route,
      'paras'  => $paras,
      'method' => $method,
    ];

    return $app['forward'] ($request, $app->url ($opt['route'], $opt['paras']), 'GET', $attributes);

  });
