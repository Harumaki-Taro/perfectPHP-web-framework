<?php

/**
 * Class to access a database. Create a child class of this class for each table.
 *
 * @var PDO $pdo
 */
abstract class DbRepository
{
  protected $pdo;

  /**
   * Constructor
   *
   * @param PDO $pdo
   */
  public function __construct($pdo)
  {
    $this->setConnection($pdo);
  }

  /**
   * Connection instance setter
   *
   * @param  PDO  $pdo
   * @return void
   */
  public function setConnection($pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * Execute the prepared statement and get the result instance.
   * [NOTICE] TODO: 連想配列ではなくて、クラスにすべき
   * $params[array] should be a multidimensional associative array below.
   * array(array('id' => ':name', 'value' => @name, 'type' => 'string' ), ...)
   *
   * @param  string       $sql
   * @param  array        $params
   * @return PDOStatement
   */
  public function execute($sql, $params = array())
  {
    try {
      if ( $stmt = $this->pdo->prepare($sql) ) {
        // Bind variables to the prepared statement as parameters
        if ( !empty($params) ) foreach ( $params as $param ) {
          switch ( $param['type'] ) {
            case 'string':
              $stmt->bindValue($param['id'], (string)$param['value'], PDO::PARAM_STR);
              break;
            case 'int':
              $stmt->bindValue($param['id'], (int)$param['value'], PDO::PARAM_INT);
              break;
            case 'bool':
              // PDO::PARAM_BOOLは使わないほうがいいかも
              $stmt->bindValue($param['id'], (boolean)$param['value'], PDO::PARAM_INT);
              break;
            case 'date':
              $stmt->bindValue($param['id'], $param['value']->format('Y-m-d'), PDO::PARAM_STR);
              break;
            case 'datetime':
              $stmt->bindValue($param['id'], $param['value']->format('Y-m-d H:i:s'), PDO::PARAM_STR);
              break;
            case 'null':
              $stmt->bindValue($param['id'], null, PDO::PARAM_NULL);
              break;
            default:
              die('ERROR: Invalid placeholder value type. ' . $param['type']);
              break;
          }
        }

        $stmt->execute();
      }
    } catch (PDOException $e) {
      die('ERROR: Could not prepare/execute query: $sql. ' . $e->getMessage());
    }

    return $stmt;
  }

  /**
   * Execute a SELECT statement and get only one row of results.
   * [NOTICE] TODO: 連想配列ではなくて、クラスにすべき
   * $params[array] should be a multidimensional associative array below.
   * array(array('id' => ':name', 'value' => @name, 'type' => 'string' ), ...)
   *
   * @param  string       $sql
   * @param  array        $params
   * @return array
   */
  public function fetch($sql, $params = array())
  {
    return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Execute a SELECT statement and get all row of results.
   * [NOTICE] TODO: 連想配列ではなくて、クラスにすべき
   * $params[array] should be a multidimensional associative array below.
   * array(array('id' => ':name', 'value' => @name, 'type' => 'string' ), ...)
   *
   * @param  string       $sql
   * @param  array        $params
   * @return array
   */
  public function fetchAll($sql, $params = array())
  {
    return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  }
}
