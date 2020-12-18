<?php

/**
 * Class for managing session infomation.
 *
 * @var bool $sessionStarted
 * @var bool $sessionIdRegenerated
 */
class Session
{
  protected static $sessionStarted       = false;
  protected static $sessionIdRegenerated = false;

  /**
   * Constructor. Automatically Start session.
   */
  public function __construct()
  {
    if ( !self::$sessionStarted ) {
      session_start();

      self::$sessionStarted = true;
    }
  }

  /**
   * Set user data on session.
   *
   * @param  string $name
   * @param  mixed  $value
   * @return void
   */
  public function set($name, $value) 
  {
    $_SESSION[$name] = $value;
  }

  /**
   * Get user data on session.
   *
   * @param  string $name
   * @param  mixed  $defalut = null
   * @return mixed
   */
  public function get($name, $default = null)
  {
    if ( isset($_SESSION[$name]) ) {
      return $_SESSION[$name];
    }

    return $default;
  }

  /**
   * Clear session data.
   *
   * @return void
   */
  public function clear()
  {
    $_SESSION = array();
  }

  /**
   * Regenerate session id.
   *
   * @param  bool  $destroy = true
   * @return void
   */
  public function regenerate($destroy = true)
  {
    if ( !self::$sessionIdRegenerated ) {
      session_regenerate_id($destroy);

      self::$sessionIdRegenerated = true;
    }
  }

  /**
   * Make the user logged in.
   * [NOTICE]
   * This method is a simple login function.
   *
   * @param  bool  $destroy = true
   * @return void
   */
  public function setAuthenticated($bool)
  {
    $this->set('_authenticated', (boolean)$bool);

    $this->regenerate();
  }

  /**
   * Check if the user is logged in.
   * [NOTICE]
   * This method is a simple login function.
   *
   * @return bool
   */
  public function isAuthenticated()
  {
    return $this->get('_authenticated', false);
  }
}
