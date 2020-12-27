<?php

class MiniBlogApplication extends Application
{
  protected $login_action = [ 'controller' => '',
                              'action'     => '', ];

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
    return [];
  }

  /**
   * Configre
   *
   * @return void
   */
  protected function configure()
  {
    $this->db_manager->connect('master',
                               [ 'driver'   => '',
                                 'dbname'   => '',
                                 'host'     => '',
                                 'port'     => '',
                                 'user'     => '',
                                 'password' => '', ]);
  }
}
