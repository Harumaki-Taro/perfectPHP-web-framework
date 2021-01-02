<?php

class UnauthorizedActionException extends RuntimeException {
  protected $requiredAuthentication;

  public function __construct(string $message, $requiredAuthentication, int $code = 0)
  {
    parent::__construct($message, $code);
    $this->requiredAuthentication = $requiredAuthentication;
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  public function getRequiredAuthentication() {
    return $this->requiredAuthentication;
  }

};
