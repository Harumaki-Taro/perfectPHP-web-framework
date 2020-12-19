<?php

/**
 * Class to manage related actions.
 *
 * @var string      $controller_name
 * @var string      $action_name
 * @var Application $application
 * @var Request     $request
 * @var Response    $response
 * @var Session     $session
 * @var DbManager   $db_manager
 */
abstract class Controller
{
  protected $controller_name;
  protected $action_name;
  protected $application;
  protected $request;
  protected $response;
  protected $session;
  protected $db_manager;

  /**
    * Constructor.
    *
    * @param Application $application
    */
  public function __construct($application)
  {
    $this->controller_name = strtolower(substr(get_class($this), 0, -10));

    $this->application = $application;
    $this->request     = $application->getRequest();
    $this->response    = $application->getResponse();
    $this->session     = $application->getSession();
    $this->db_manager  = $application->getDbManager();
  }

  /**
   * Execute action
   *
   * @param  string $action
   * @param  array  $params
   * @return string
   */
  public function run($action, $params = array())
  {
    $this->action_name = $action;

    $action_method = $this->action_name . 'Action';
    if ( ! method_exists($this, $action_method) ) {
      $this->forward404();
    }

    $content = $this->$action_method($params);

    return $content;
  }

  /**
   * Load the view file corresponding to $this->action_name or $template. Wrapper for View.render()
   * method.
   *
   * @param  array  $variables
   * @param  string $template = null
   * @param  string $layout = 'layout'
   * @return string
   */
  protected function render($variables = array(), $template = null, $layout = 'layout')
  {
    $defaults = array(
      'request'  => $this->request,
      'base_url' => $this->request->getBaseUrl(),
      'session'  => $this->session,
    );

    $view = new View($this->application->getViewDir(), $defaults);

    if ( is_null($template) ) {
      $template = $this->action_name;
    }

    $path = $this->controller_name . '/' . $template;

    return $view->render($path, $variables, $layout);
  }

  /**
   * Notify HttpNotFoundException and prompt for transition to 404 error page.
   *
   * @throws HttpNotFoundException
   * @return void
   */
  protected function forward404()
  {
    throw new HttpNotFoundException(
      'Forward 404 page from ' . $this->controller_name . '/' . $this->action_name
    );
  }

  /**
   * Set a redirection to the specified URL in Response instance.
   * If you want to redirect different actions in the same application, you can specify PATH_INFO
   * only.
   *
   * @param  string $url
   * @return void
   */
  protected function redirect($url)
  {
    // in the case of relative URL
    if ( !preg_match('#https?://#', $url) ) {
      $protocol = $this->request->isSel() ? 'https://' : 'http://';
      $host     = $this->request->getHost();
      $base_url = $this->redirect->getBaseUrl();

      $url = $protocol . $host . $base_url . $url;
    }

    $this->response->setStatusCode(302, 'Found');
    $this->response->setHttpHeader('Location', $url);
  }

}
