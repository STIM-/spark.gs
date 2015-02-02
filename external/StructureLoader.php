<?php


  $app['StructureLoader'] = $app->protect (function ($file, $root) use ($app) {

    if (! is_array ($file))
      $app['ExistsLoader'] ($file, $root);

    else {
      foreach ($file as $p => $f) {

        if (is_array ($f))
          $app['StructureLoader'] ($f, "$root/$p");

        else
          $app['ExistsLoader'] ($f, $root);
      }
    }
  });


  $app['ExistsLoader'] = $app->protect (function ($file, $root) use ($app) {

    file_exists ("$root/$file.php") && require_once "$root/$file.php";
  });