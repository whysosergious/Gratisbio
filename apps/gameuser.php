<?
//game-user.php

//header("Content-type: text/plain");

include("../classes/game.php");
include("../class_portal.php");
include("../class_login.php");

$game = new game;
$portal = new cPortal("../");
$user = new cLogIn('User');
$uid = "null";

if($user->IsLogedIn()) {
	
	$uid = $user->GetUserID() * 4444;
	
	list($userCredits, $userKind) = mysql_fetch_row(
		
		mysql_query('
			
			SELECT Points, RecruiterID
			FROM Users
			WHERE ID = '.$user->GetUserID().'
			
		')
		
	);
	$welcomePoints = (is_null($userKind) ? 50 : 75); //the lock on free welcome points
	if($_POST) {
		
		$userCredits = (isset($_POST['credits']) ? $_POST['credits'] / $uid + $welcomePoints : 0);
		$winAmount = (isset($_POST['winAmount']) ? $_POST['winAmount'] / $uid : 0);
		
	}
	else {
		
		$userCredits = $userCredits;
		
	}
	if($_POST['userBet']>0) {
		
		mysql_query('
			
			INSERT INTO GameHistory(
				
				UserID,
				Headline,
				Points,
				Date
				
			)
			VALUES(
				
				'.$user->GetUserID().',
				"'.GAME.FEE.'",
				-'.$_POST['userBet'] / $uid.',
				'.time().'
				
			);
			
		');
	}
	if($winAmount>0 && $_POST['playState'] != "Lost") {
		
		mysql_query('
			
			INSERT INTO GameHistory(
				
				UserID,
				Headline,
				Points,
				Date
				
			)
			VALUES(
				
				'.$user->GetUserID().',
				"'.GAME.(isset($_POST['playState']) ? $_POST['playState'] : WON).'",
				'.$winAmount.',
				'.time().'
				
			);
			
		');
		
	}
	$userCredits -= $welcomePoints;
	if(mysql_query('
		
		UPDATE Users
		SET Points = '.($userCredits + $welcomePoints).'
		WHERE ID = '.$user->GetUserID().';
		
	')) {
		echo "write=success";
	} else {
		echo "write=fail";
	}
	
	if($userCredits < 0) {
		
		echo "&userCredits=0";
		
	}
	else echo "&userCredits=".floor($userCredits) * $uid."&userCreditsII=".floor($userCredits) * $uid;
	
}
else echo "&userCredits=0";

mysql_close();

?>