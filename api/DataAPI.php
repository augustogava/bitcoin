<?php
require "adodb/adodb.inc.php";
require "Configuracao.php";

class DataAPI{

	function DataAPI() {
		$this->connection = new mysqli(Configuracao :: $MYSQL_HOSTNAME, Configuracao :: $MYSQL_USER, Configuracao :: $MYSQL_PASSWORD, Configuracao :: $MYSQL_DATABASE);
	}
	
	function saveBitValue($avg, $tend, $diff, $perc){
		$query = "INSERT INTO bit_value VALUES(null, now(), '".$avg."', '".$tend."', '".$diff."', '".$perc."')";

		$this->connection->query($query);
		
	}
	
// 	function save($parametro){
		
// 		$query = "SELECT value FROM parameter where name = '".$parametro."' ";
		
// 		$rs = $this->connection->Execute($query);
// 		$rs = $rs->GetArray();
		
// 		if ($rs != null && count($rs) > 0) {
// 			return $rs[0]["value"];
// 		}
		
// 	}
}
?>