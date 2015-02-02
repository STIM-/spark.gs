<?php

  use Silex\Application;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\HttpKernelInterface;
  use Symfony\Component\Yaml\Yaml;


  /**
   *
   * Root
   *
   */
  define ('_ROOT', dirname (__DIR__));


  /**
   *
   * Upload
   *
   */
  define ('_UPLOAD', _ROOT .'/public/land');


  /**
   *
   * Vendors
   *
   */
  require_once _ROOT . '/vendor/autoload.php';


  /**
   *
   * Environments and Defines
   *
   */
  $env = Yaml::parse (_ROOT . '/env.yml');

  define ('_HOST',       'http://' . $env['web']['host']);
  define ('_BASE',       rtrim (dirname ($_SERVER['SCRIPT_NAME']), '/'));
  define ('_HTTP',       _HOST . _BASE);
  define ('_URI',        str_replace ('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));

  define ('_TIMESTAMP',  time ());
  define ('_DATE',       date ('Y-m-d', _TIMESTAMP));
  define ('_TIME',       date ('H:i:s', _TIMESTAMP));
  define ('_DATETIME',   _DATE .' '. _TIME);

  define ('_SECRET',     'sPaRk.Gs');


  /**
   *
   * Externals (before $app generate)
   *
   */
  //require_once _ROOT . '/external/I18n.php';
  require_once _ROOT . '/external/TwigTraitAdapter.php';
  require_once _ROOT . '/external/Pager.php';


  /**
   *
   * Initial application
   *
   */

  class _Application extends Silex\Application {

    use TwigTraitAdapter;
    use Silex\Application\UrlGeneratorTrait;
    use Silex\Application\FormTrait;
    use Silex\Application\SwiftmailerTrait;
    /*
    use Silex\Application\SecurityTrait;
    use Silex\Application\FormTrait;
    use Silex\Application\MonologTrait;
    use Silex\Application\TranslationTrait;
    */
  }

  $app = new _Application ();


  /**
   *
   * Vendors & Externals (after $app generate)
   *
   */
  require_once _ROOT . '/external/StructureLoader.php';
  require_once _ROOT . '/external/Validation.php';
  require_once _ROOT . '/external/Forward.php';
  require_once _ROOT . '/external/File.php';
  require_once _ROOT . '/external/Bower.php';


  /**
   *
   * Application evironments
   *
   */
  $app['env'] = $env;


  /**
   *
   * Debug mode
   *
   */
  $sys = $env['system'];

  if (isset ($_GET['bug']) && (isset ($sys['debug']) && $sys['debug'] == 1))
    $app['debug'] = true;

  if (isset ($sys['force_debug']) && $sys['force_debug'] == 1)
    $app['debug'] = true;


  /**
   *
   * Register services
   *
   */

  // Seervice Controller Provider
  $app->register (new Silex\Provider\ServiceControllerServiceProvider ());

  // Url Generator
  $app->register (new Silex\Provider\UrlGeneratorServiceProvider ());

  // Validator
  $app->register(new Silex\Provider\ValidatorServiceProvider ());

  // Form
  $app->register(new Silex\Provider\FormServiceProvider ());

  // Translation
  $app->register(new Silex\Provider\TranslationServiceProvider (), array(
    'locale_fallbacks' => array ('zh_TW'),
  ));

  // Session
  $app->register(new Silex\Provider\SessionServiceProvider ());

  $app['session']->registerBag (new Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag ());

  // Twig
  $app->register (new Silex\Provider\TwigServiceProvider (), array (
    'twig.path'    => _ROOT . '/view',
    'twig.options' => array ('cache' => _ROOT . '/caches', 'auto_reload' => true),
  ));

  //$app['twig']->addExtension (new Twig_Extensions_Extension_I18n ());
  $app['twig']->addFunction (new Twig_SimpleFunction ('die', 'die'));
  $app['twig']->addFunction (new Twig_SimpleFunction ('set', function ($key, $val) use ($app) { $app['twig.' . $key] = $val; }));
  $app['twig']->addFilter (new Twig_SimpleFilter ('is_*', function ($page, $input, $ret = 'active') { return $input == $page ? $ret : ''; }));


  /**
   *
   * Boot registered services
   *
   */
  $app->boot ();


  /**
   *
   * Paris & Idiorm configurations
   *
   */
  $database = $app['env']['database'];

  ORM::configure ('mysql:host='. $database['host'] .';dbname='. $database['database']);
  ORM::configure ('username', $database['account']);
  ORM::configure ('password', $database['password']);

  ORM::configure ('logging',  $database['logging']);
  ORM::configure ('caching',  $database['caching']);
  ORM::configure ('driver_options', array (PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));


  /**
   *
   * Load database models
   *
   */
  $app['StructureLoader'] ([], _ROOT . '/model');


  /**
   *
   * Load Controllers
   *
   */
  $app['StructureLoader'] (['main'], _ROOT . '/app');


  /**
   *
   * Login check
   *
   */
  $app->before (function (Request $request) use ($app) {

    $app['login'] = false;

    /*
    if ($app['session']->get ('login') == true && is_array ($app['session']->get ('user'))) {
      $app['login'] = true;
      $app['user'] = Model::factory ('Member')->find_one ($app['session']->get ('user/id'));
    }
    */

  }, 1);


  /**
   *
   * execute
   *
   */
  $app->run ();

  /*
  if (isset ($_GET['bug']) && $app['debug'])
    echo '<pre>' . var_export (ORM::get_query_log (), true);

  else if ($app['debug'])
    echo "<!-- \n" . var_export (ORM::get_query_log (), true) . "\n -->";
  */