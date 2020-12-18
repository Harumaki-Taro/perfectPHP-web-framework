<?php

/**
 * Class to manage a server response.
 *
 * @var mixed  $content
 * @var int    $status_code
 * @var string $status_text
 * @var array  $http_headers
 */
class Response
{
  protected $content;
  protected $status_code  = 200;
  protected $status_text  = 'OK';
  protected $http_headers = array();

  /**
   * Send a response.
   *
   * @return null
   */
  public function send()
  {
    header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_text);

    foreach ( $this->http_headers as $name => $value ) {
      header($name . ': ' . $value);
    }

    echo $this->content;
  }

  /**
   * Set a content such as HTML.
   *
   * @return null
   */
  public function setContent($content)
  {
    $this->content = $content;
  }

  /**
   * Set a status
   *
   * @param  int    $status_code
   * @param  string $status_text
   * @return null
   */
  public function setStatusCode($status_code, $status_text = '')
  {
    $this->status_code = $status_code;
    $this->status_text = $status_text;
  }

  /**
   * Set HTTP header
   *
   * @param  string $name
   * @param  string $value
   * @return null
   */
  public function setHttpHeader($name, $value)
  {
    $this->http_headers[$name] = $value;
  }
}
