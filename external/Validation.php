<?php


  $app['violation.parser'] = $app->protect (function ($violation) use ($app) {

    $result = [];

    foreach ($violation as $v) {

      $field = $v->getPropertyPath ();
      $field = trim ($field, '[]');

      $result[$field]['message'] = $v->getMessage ();
      $result[$field]['value']   = $v->getInvalidValue ();
    }

    return $result;

  });