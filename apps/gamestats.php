<?
//game-stats.php

function round_down($num, $dec) {
	
	$multi = 10 * $dec;
	$num = $num * $multi;
	$num = floor($num);
	$num = $num/$multi;
	return $num;
	
}

include("../classes/game.php");
include("../class_portal.php");
include("../class_login.php");

$game = new game;
$portal = new cPortal("../");
$user = new cLogIn('User');

$result = mysql_query("SELECT totalWin, totalLoss, medPot, prize, medPotLoss, prizeLoss FROM gratisbi_general.gaming WHERE id = 1;");
list($totalWin, $totalLoss, $medPot, $prize, $medPotLoss, $prizeLoss) = mysql_fetch_row($result);

if(!empty($_POST)) {
$totalWin += round_down($_POST["credWin"], 2);
$totalLoss += round_down($_POST["credLoss"], 2);
$safe = round_down($_POST["safe"], 2);
$prize += round_down($_POST["jackPot"] - $_POST["prizeLoss"], 2);
$medPot += round_down($_POST["medPrize"] - $_POST["medPotLoss"], 2);
$medPotLoss += round_down($_POST["medPotLoss"], 2);
$prizeLoss += round_down($_POST["prizeLoss"], 2);
} else {
	$safe = 0;
}

$house = round_down(($totalWin - $totalLoss), 2);

$save = mysql_query("UPDATE gratisbi_general.gaming SET totalWin = $totalWin, totalLoss = $totalLoss, house = $house, safe = safe + $safe, prize = $prize, medPot = $medPot, medPotLoss = $medPotLoss, prizeLoss = $prizeLoss  WHERE id = 1;");

echo "house=" . $house . "&" . "prize=" . $prize . "&" . "medPot=" . $medPot;

mysql_close();

?>