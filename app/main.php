<?php

  use Silex\Application;
  use Symfony\Component\Validator\Validation;
  use Symfony\Component\Validator\Constraints as Assert;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\HttpKernelInterface;
  use Symfony\Component\Form\FormError;


  /**
   *
   * Home
   *
   */
  $app->get ('/', function (Request $request) use ($app) {

    return $app
      ->render ('index.html');

  })->bind ('index');
