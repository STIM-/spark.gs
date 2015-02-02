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
  $app['bower.asset.hash'] = $app->protect (function ($bower) use ($app) {

    $text = serialize ($bower['dependencies']);

    $jstext  = '';
    $csstext = '';

    if (isset ($bower['asset'])) {
      if (isset ($bower['asset']['js'])) {

        $jstext = $text . serialize ($bower['asset']['js']);

        if (isset ($bower['asset']['js']['general'])) {
          foreach ($bower['asset']['js']['general'] as $file)
            $jstext .= file_exists (_ROOT . $file) ? filemtime (_ROOT . $file) : '';
        }
      }
    }

    if (isset ($bower['asset'])) {
      if (isset ($bower['asset']['css'])) {

        $csstext = $text . serialize ($bower['asset']['css']);

        if (isset ($bower['asset']['css']['general'])) {
          foreach ($bower['asset']['css']['general'] as $file)
            $csstext .= file_exists (_ROOT . $file) ? filemtime (_ROOT . $file) : '';
        }
      }
    }

    $jshash  = md5 ($jstext);
    $csshash = md5 ($csstext);

    return [
      'js'  => $jshash,
      'css' => $csshash
    ];

  });
