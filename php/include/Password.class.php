<?php

/**
 * Password hashing and verifying
 *
 * Hashes and verifies passwords (replaces MD5 hashing).
 *
 * @package Password
 */

class Password {
  private $password;
  
  public function __construct($password) {
    $this->password = $password;
  }

  public function hash() {
    return password_hash($this->password, PASSWORD_DEFAULT);
  }

  private function isMd5($md5 = '') {
    return preg_match('/^[a-f0-9]{32}$/', $md5);
  }
  
  public function verify($hash) {
    // This is ugly from a security perspective, but lets users
    // keep using their old MD5 hashes. Administrators are
    // encouraged to change all concerned passwords which will
    // automatically switch to the new hashing method.
    if ($this->isMd5($hash))
      return md5($this->password) == $hash;
    else
      return password_verify($this->password, $hash);
  }
}

?>