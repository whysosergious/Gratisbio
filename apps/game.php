<?
//game.php

class game {
	
	var $totalWin = 0;
	var $totalLoss = 0;
	var $medPot = 0;
	var $prize = 0;
	var $medPotLoss = 0;
	var $prizeLoss = 0;
	var $safe = 0;
	var $house = 0;
	function round_down($num, $dec) {
		
		$multi = 10 * $dec;
		$num = $num * $multi;
		$num = floor($num);
		$num = $num / $multi;
		return $num;
		
	} //round_down()
	function initialize($game) {
		
		
		
	} //initialize()
	function info($name, $columns = '*') {
		
		$data = mysql_query('
			
			SELECT '.$columns.'
			FROM games
			WHERE name = "'.$name.'";
			
		');
		
	} //info()
	function stats() {
		
		
	} // stats()	
	
} //game

?>