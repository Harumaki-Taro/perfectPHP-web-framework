<?php

/**
 * Class for managing session infomation.
 *
 * @var bool  $sessionStarted
 * @var bool  $sessionIdRegenerated
 * @var array $authentications
 */
class Session
{
  protected static $sessionStarted       = false;
  protected static $sessionIdRegenerated = false;
  protected $authentications = [true,];

  /**
   * Constructor. Automatically Start session.
   */
  public function __construct($authentications=[true,])
  {
    if ( !self::$sessionStarted ) {
      session_start();

      self::$sessionStarted = true;
    }

    $this->authentications = $authentications;
    if ( $this->get('_authenticated') === null ) {
      $this->setAuthenticated(false);
    }
  }

  /**
   * Set user data on session.
   *
   * @param  string $key
   * @param  mixed  $value
   * @return void
   */
  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * isset session data.
   *
   * @param  string $key
   * @return bool
   */
  public function isset($key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Get user data on session.
   *
   * @param  string $key
   * @param  mixed  $defalut = null
   * @return mixed
   */
  public function get($key, $default = null)
  {
    return $_SESSION[$key] ?? $default;
  }

  /**
   * Clear session data.
   * [NOTICE]
   * If you want to completely delete the data registered in the session, use the $destroy flag.
   * However, when you use set_authenticated() immediately after, it is not necessary.
   *
   * @param  bool $destroy
   * @return void
   */
  public function clear($destroy=false)
  {
    $_SESSION = array();
    if ( ini_get("session.use_cookies") ) {
      $params = session_get_cookie_params();
      setcookie(session_name(),
                '',
                time()-42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']);
    }

    if ( $destroy ) {
      session_destroy();
    }
  }

  /**
   * Clear session data corresponding to $key.
   *
   * @return void
   */
  public function unset($key)
  {
    if ( isset($_SESSION[$key]) ) {
      unset($_SESSION[$key]);
    }
  }

  /**
   * Regenerate session id.
   *
   * @param  bool $destroy = true
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
   *
   * @param  string|bool $authentication
   * @return bool
   */
  public function setAuthenticated($authentication)
  {
    $this->regenerate();

    if ( in_array($authentication, $this->authentications) || $authentication === false ) {
      $this->set('_authenticated', $authentication);

      return true;
    } else {
      $this->set('_authenticated', false);

      return false;
    }
  }

  /**
   * Check if the user is logged in.
   *
   * @retuen string|bool
   */
  public function isAuthenticated()
  {
    return $this->get('_authenticated', false);
  }

  /**
   * Get allowed authentications.
   *
   * @return array
   */
  public function getAuthentications()
  {
    return $this->authentications;
  }
}
