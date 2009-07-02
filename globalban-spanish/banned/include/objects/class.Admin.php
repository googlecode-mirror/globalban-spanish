<?php

class Admin{ 
  var $id;
  var $admin;
  
  function __construct() {
  }
  
	function Admin() {
	}
	
	function init() {
    $this->id = -1;
    $this->admin = "Administrador";
  }
  
  function getId() {
    return $this->id;
  }
  
  function getAdmin() {
    return stripslashes($this->admin);
  }
  
  function setId($id) {
    $this->id = $id;
  }
  
  function setAdmin($admin) {
    $this->admin = $admin;
  }
}
?>
