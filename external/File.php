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
  $app['file.upload'] = $app->protect (function ($file, $path) use ($app) {

    if ($file) {

      $ori = $file->getClientOriginalName ();
      $ext = $file->getClientOriginalExtension ();
      $new = sha1_file ($file->getPathName ()) . ".$ext";

      $path = "$path/$new[0]/$new[1]/$new[2]";

      if (! is_dir ($path))
        mkdir ($path, 0700, true);

      $file->move ($path, $new);
      $file = str_replace (_ROOT . '/public/', '', "$path/$new");
    }

    else
      $file = '';

    return $file;
  });
