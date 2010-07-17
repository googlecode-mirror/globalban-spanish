<?php
/*
    This file is part of GlobalBan.

    Written by Stefan Jonasson <soynuts@unbuinc.net>
    Copyright 2008 Stefan Jonasson
    
    GlobalBan is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    GlobalBan is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GlobalBan.  If not, see <http://www.gnu.org/licenses/>.
*/

  /****************************************************************************************************** 
    This class is stores information about a banned user
   ******************************************************************************************************/

class ReasonStats {
	
	// Variables
	var $Motivo;
	var $Motivo_id;
	var $NumBaneados;
	var $NumCumplidos;
	var $NumCumpliendose;
	var $NumPermanentes;
	
	// Default Constructor (PHP 5)
	function __construct() {
	}
	
	// Default Constructor (PHP 4)
	function ReasonStats() {
	}

	function init() {
	}
	

	
 /************************************************************************
   Accessor Methods
  ************************************************************************/
  function getMotivo() {
    return $this->Motivo;
  }
  
  function getMotivo_id() {
    return $this->Motivo_id;
  }

  function getNumBaneados() {
    return $this->NumBaneados;
  }
  
  function getNumCumplidos() {
    return $this->NumCumplidos;
  }

  function getNumCumpliendose() {
    return $this->NumCumpliendose;
  }

  function getNumPermanentes() {
    return $this->NumPermanentes;
  }
  
  function getPorcCumpl() {
    return ($this->NumCumplidos / $this->NumBaneados * 100);
  }

  function getPorcCumpls() {
    return ($this->NumCumpliendose / $this->NumBaneados * 100);
  }

  function getPorcPerm() {
    return ($this->NumPermanentes / $this->NumBaneados * 100);
  }

 /************************************************************************
   Mutator Methods
  ************************************************************************/
  
  function setMotivo($Motivo) {
    $this->Motivo = $Motivo;
  }

  function setMotivo_id($Motivo_id) {
    $this->Motivo_id = $Motivo_id;
  }
	
  function setNumBaneados($NumBaneados) {
    $this->NumBaneados = $NumBaneados;
  }
  
  function setNumCumplidos($NumCumplidos) {
    $this->NumCumplidos = $NumCumplidos;
  }
  
  function setNumCumpliendose($NumCumpliendose) {
    $this->NumCumpliendose = $NumCumpliendose;
  }
  
  function setNumPermanentes($NumPermanentes) {
    $this->NumPermanentes = $NumPermanentes;
  }
}
?>
