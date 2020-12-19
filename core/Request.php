<?php

/**
 * Class to controll client request information.
 */
class Request
{
  /**
   * Check if the HTTP method is POST or not.
   *
   * @return bool
   */
  public function isPost()
  {
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
      return true;
    }

    return false;
  }

  /**
   * Get a variable passed to the current script in URL parameters (query strings).
   *
   * @param  string $name
   * @param  string $default = null
   * @return string
   */
  public function getGet($name, $default = null)
  {
    if ( isset($_GET[$name]) ) {
      return $_GET[$name];
    }

    return (string)$default;
  }

  /**
   * Get a variable passed to the current script from the HTTP POST method.
   *
   * @param  string $name
   * @param  string $default = null
   * @return string
   */
  public function getPost($name, $default = null)
  {
    if ( isset($_POST[$name]) ) {
      return $_POST[$name];
    }

    return (string)$default;
  }

  /**
   * Get the server hostname.
   *
   * @return string
   */
  public function getHost()
  {
    if ( !empty($_SERVER['HTTP_HOST']) ) {
      return $_SERVER['HTTP_HOST'];
    }

    return $_SERVER['SERVER_NAME'];
  }

  /**
   * Check if the site was accessed using HTTPS.
   *
   * @return bool
   */
  public function isSel()
  {
    if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ) {
      return true;
    }

    return false;
  }

  /**
   * Get requested URI.
   *
   * @return string
   */
  public function getRequestUri()
  {
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Get requested base url. (URL: http://hostname/base-url/path-info?query)
   *
   * @return string
   */
  public function getBaseUrl()
  {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $request_uri = $this->getRequestUri();

    if ( 0 === strpos($request_uri, $script_name) ) {
      return $script_name;
    } elseif ( 0 === strpos($request_uri, dirname($script_name)) ) {
      return rtrim(dirname($script_name), '/');
    }

    return '';
  }

  /**
   * Get requested path info. (URL: http://hostname/base-url/path-info?query)
   *
   * @return string
   */
  public function getPathInfo()
  {
    $base_url    = $this->getBaseUrl();
    $request_uri = $this->getRequestUri();

    if ( false !== ($pos = strpos($request_uri, '?')) ) {
      $request_uri = substr($request_uri, 0, $pos);
    }

    $path_info = (string)substr($request_uri, strlen($base_url));

    return $path_info;
  }
}
