<?php

/**
 * [Abstract] Class to manage the entire application.
 * - manage instance of Request, Router, Response, Session, DbManager.
 * - define routing, execute controller, send response, etc.
 * - manage the path to the application directory.
 * - manage debug mode.
 *
 * @var bool      $debug = false
 * @var Request   $request
 * @var Response  $response
 * @var Session   $session
 * @var DbManager $db_manager
 * @var Router    $router
 * @var array     $login_action = array() [$controller_name, $action]
 */
abstract class Application
{
  protected $debug = false;
  protected $request;
  protected $response;
  protected $session;
  protected $db_manager;
  protected $router;
  protected $login_action = array();

  /**
   * Constructor.
   *
   * @param bool $debug = false
   */
  public function __construct($debug = false)
  {
    $this->setDebugMode($debug);
    $this->initialize();
    $this->configure();
  }

  /**
   * Set debug mode. Change the error display process.
   *
   * @param  bool $debug
   * @return void
   */
  protected function setDebugMode($debug)
  {
    if ( $debug ) {
      $this->debug = true;
      ini_set('display_errors', 'On');
      error_reporting(E_ALL);
    } else {
      $this->debug = false;
      ini_set('display_errors', 'Off');
    }
  }

  /**
   * Initialize application.
   *
   * @return void
   */
  protected function initialize()
  {
    $this->request    = new Request();
    $this->response   = new Response();
    $this->session    = new Session();
    $this->db_manager = new DbManager();
    $this->router     = new Router($this->registerRoutes());
  }

  /**
   * Set by individual application.
   *
   * @return void
   */
  protected function configure()
  {
    // pass
  }

  /**
   * [Abstract] Return the path to root directory of the application. It is provided so that the
   * sdirectory tructure can be changed arbitrarily if necessary.
   */
  abstract public function getRootDir();

  /**
   * [Abstract] Return a routing definition array for each individual application.
   */
  abstract public function registerRoutes();

  /**
   * Check if it is debug mode.
   *
   * @return bool
   */
  public function isDebugMode()
  {
    return $this->debug;
  }

  /**
   * Get request instance.
   *
   * @return Request
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Get response instance.
   *
   * @return Response
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Get session instance.
   *
   * @return Session
   */
  public function getSession()
  {
    return $this->session;
  }

  /**
   * Get DbManager instance.
   *
   * @return DbManager
   */
  public function getDbManager()
  {
    return $this->db_manager;
  }

  /**
   * Get controller directory.
   *
   * @return string
   */
  public function getControllerDir()
  {
    return $this->getRootDir() . '/controllers';
  }

  /**
   * Get view directory.
   *
   * @return string
   */
  public function getViewDir()
  {
    return $this->getRootDir() . '/views';
  }

  /**
   * Get model directory.
   *
   * @return string
   */
  public function getModelDir()
  {
    return $this->getRootDir() . '/models';
  }

  /**
   * Get web directory.
   *
   * @return string
   */
  public function getWebDir()
  {
    return $this->getRootDir() . '/web';
  }

  /**
   * Trigger the application to respond to the user request and return a response.
   *
   * @return void
   */
  public function run()
  {
    try {
      $params = $this->router->resolve($this->request->getPathInfo());

      if ( $params === false ) {
        throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
      }

      $controller = $params['controller'];
      $action     = $params['action'];

      $this->runAction($controller, $action, $params);

    } catch ( HttpNotFoundException $e ) {
      $this->render404Page($e);

    } catch ( UnauthorizedActionException $e ) {
      list($controller, $action) = $this->login_action;
      $this->runAction($controller, $action);
    }

    $this->response->send();
  }

  /**
   * Take a controller name and an action name, and execute the action.
   *
   * @param  string $controller_name
   * @param  string $action
   * @param  array  $params = array()
   * @return void
   */
  public function runAction($controller_name, $action, $params = array())
  {
    $controller_class = ucfirst($controller_name) . 'Controller';

    $controller = $this->findController($controller_class);
    if ( $controller === false ) {
      throw new HttpNotFoundException($controller_class . ' controller is not found.');
    }

    $content = $controller->run($action, $params);
    $this->response->setContent($content);
  }

  /**
   * Load the class file when the controller class is not loaded.
   *
   * @param  string     $controller_class
   * @return Controller
   */
  protected function findController($controller_class)
  {
    if ( !class_exists($controller_class) ) {
      $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

      if ( !is_readable($controller_file) ) {
        return false;
      } else {
        require_once $controller_file;

        if ( !class_exists($controller_class) ) {
          return false;
        }
      }
    }

    return new $controller_class($this);
  }

  /**
   * Render 404 Not Found Page
   *
   * @param  Exception $e
   * @return void
   */
  protected function render404Page($e)
  {
    $this->response->setStatusCode(404, 'Not Found');
    $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
  <title>404</title>
</head>
<body>
  {$message}
</body>
</html>
EOF
    );
  }
}
