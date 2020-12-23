<?php

class WebApplication extends Application
{
  protected $login_action = array('account', 'signout');

  /**
   * Return root directory.
   *
   * @return void
   */
  public function getRootDir()
  {
    return dirname(__FILE__) . '/..';
  }

  /**
   * Return routing defined array.
   *
   * @return array
   */
  public function registerRoutes()
  {
    return array(
    );
  }

  /**
   * Configre
   *
   * @return void
   */
  protected function configure()
  {
    $this->db_manager->connect('master', array(
      'driver'   => '',
      'dbname'   => '',
      'host'     => '',
      'port'     => '',
      'user'     => '',
      'password' => ''
    ));
  }
}
