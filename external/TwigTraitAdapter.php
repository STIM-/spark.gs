<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Twig trait.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
trait TwigTraitAdapter
{


  var $view = '';


  /**
   * Assign varibles
   *
   */
  public function assign ($key, $var)
  {
    if (! isset ($this['twig.paras']))
      $this['twig.paras'] = [];

    $paras = $this['twig.paras'];

    if (isset ($paras[$key]))
      $paras[$key] = is_array ($paras[$key]) ? ($paras[$key] + (array) $var) : $var;

    else
      $paras += array ($key => $var);

    $this['twig.paras'] = $paras;

    return $this;
  }

  /**
   * Renders a view and returns a Response.
   *
   * To stream a view, pass an instance of StreamedResponse as a third argument.
   *
   * @param string   $view       The view name
   * @param array    $parameters An array of parameters to pass to the view
   * @param Response $response   A Response instance
   *
   * @return Response A Response instance
   */
  public function render($view, array $parameters = array(), Response $response = null)
  {

    $this->view = $view;

    if (isset ($this['twig.paras']))
      $parameters += $this['twig.paras'];

    if (null === $response) {
      $response = new Response();
    }

    $twig = $this['twig'];

    if ($response instanceof StreamedResponse) {
      $response->setCallback(function () use ($twig, $view, $parameters) {
        $twig->display($view, $parameters);
      });
    } else {
      $response->setContent($twig->render($view, $parameters));
    }

    return $response;
  }

  /**
   * Renders a view.
   *
   * @param string $view       The view name
   * @param array  $parameters An array of parameters to pass to the view
   *
   * @return Response A Response instance
   */
  public function renderView($view, array $parameters = array())
  {

    $this->view = $view;

    return $this['twig']->render($view, $parameters);
  }

  /**
   * Renders a view.
   *
   * @param string $view       The view name
   * @param array  $parameters An array of parameters to pass to the view
   *
   * @return Response A Response instance
   */
  public function getRenderedView ()
  {
    return $this->view;
  }
}
