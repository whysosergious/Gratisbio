<?
//gamecash.php

/******************************************************************************************

1. recieves $_POST['hollar'] (writes IsPlayning & GameName);
2. sends userCredits, userCreditsII & gameInPlay(hollar);
3. recieves $_POST['hollar'], $_POST['userBet'], $_POST['winAmount'], $_POST['playState']

******************************************************************************************/

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include("../classes/game.php");
include("../class_portal.php");
include("../class_login.php");

$game = new game;
$portal = new cPortal("../");
$user = new cLogIn('User');
$uid = "null";
$salt = 4444; //crypt

//language
include("../lang/".$portal->Country."/lang.php");

//run only if user is logged in
if($user->IsLogedIn()) {
	
	//define main variables
	$uid = $user->GetUserID();
	$crypt = $uid * $salt;
	//retrieave user info from databse
	list($userCredits, $isRecruited, $userState, $hollar) = mysql_fetch_array(
		
		mysql_query('
			
			SELECT Points, RecruiterID, IsPlaying, GameName
			FROM Users
			WHERE ID = '.$uid.';
			
		')
		
	);
	//1. start if not started
	if(isset($_POST['hollar']) && $userState == 0 && $hollar == "") {
		
		mysql_query('
			
			UPDATE Users
			SET IsPlaying = '.time().', GameName = "'.$_POST['hollar'].'"
			WHERE ID = '.$uid.';
			
		');
		
	}
	//3. write bookie row
	elseif(isset($_POST['userBet']) && isset($_POST['winAmount']) && isset($_POST['playState'])) {
		
		//as game's name looks like TREASURE_CHEST_736, we must do some manipulations
		$inparts = explode("_", $_POST['gameName']);
		unset($inparts[count($inparts)-1]);
		$gamename = implode("_", $inparts);
		//user bet
		if($_POST['userBet'] > 0) {
			
			mysql_query('
				
				INSERT INTO GameHistory(UserID, Headline, Date, Points, Bookie)
				VALUES('.$uid.', "'.str_replace("%GameName%", constant($gamename), BET).'", '.time().', '.($_POST['userBet'] / $crypt * -1).', 0);
				
			');
			
		}
		//user won
		if($_POST['winAmount'] > 0) {
			
			mysql_query('
				
				INSERT INTO GameHistory(UserID, Headline, Date, Points, Bookie)
				VALUES('.$uid.', "'.(@defined($_POST['playState']) ? str_replace("%GameName%", constant($gamename), constant($_POST['playState'])) : constant($gamename).", ".$_POST['playState']).'", '.time().', '.($_POST['winAmount'] / $crypt * -1).', 0);
				
			');
			
		}
		//update user IsPlaying status
		mysql_query('
			
			UPDATE Users
			SET IsPlaying = '.time().'
			WHERE ID = '.$uid.';
			
		');
		
	}
	//2. echo credits
	else {
		
		$userCredits -= is_null($isRecruited) ? 50 : 75;
		$userCredits = ($userCredits < 0 ? 0 : $userCredits) * $crypt;
		echo "userCredits=".$userCredits
			."&userCreditsII=".$userCredits
			."&gameInPlay=".($hollar == "" ? 0 : $hollar)
			."&lng_play=".lng_play;
		
	}
	
}
else {
	
	echo "You're not logged in!";
	
}
mysql_close();

?>