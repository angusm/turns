<?php

//This class will be used to make all those sick as shit menu items that will run the show
App::uses('AppHelper', 'View/Helper');

/**
 * Class TurnMenuHelper
 */
class TurnMenuHelper extends AppHelper {
	
	//We'll be using some of the HTML helper's functionality to do awesome stuff
  	var $helpers = ['Html','TurnForm'];
	
	//PUBLIC FUNCTION: availableGameList
	//Return a list of all of the games the user is currently involved in
	//along with the option to jump into playing the games
	/**
	 * @return string
	 */
	public function availableGameList(){
		
		//Establish a return string
		$returnString = '';
		
		//Initialize the user game model so that we can use it
		$userGameModelInstance = ClassRegistry::init('UserGame');
		
		//Grab all of the games that the user is involved in 
		$userGames = $userGameModelInstance->getGamesByUserUID( AuthComponent::user('uid') );
		
		//Loop through each game and create a play block for it
		foreach( $userGames as $userGame ){
			
			$returnString .= $this->playGameButton( $userGame['UserGame']['games_uid'] );
			
		}
		
		//We also want to make note of the times the user has entered the queue
		//To do this we need to initialize a MatchmakingQueue model and find them all
		$matchmakingQueueModelInstance = ClassRegistry::init('MatchmakingQueue');
		$pendingMatches = $matchmakingQueueModelInstance->getPendingGamesByUserUID( AuthComponent::user('uid') );
		
		//Loop through the pending matches and display a blurb for it
		foreach( $pendingMatches as $pendingMatch ){
		
			$returnString .= $this->pendingMatchBlurb( $pendingMatch['MatchmakingQueue']['uid'] );
			
		}
		
		//And slam everything in a div and return it
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										[
											'class' => 'availableGames'
										]
									);
									
		//Add in the libraries that are needed to make this part run
		$returnString .= $this->Html->tag(	
						'script',
						'if( typeof window.pageLibraries === "undefined" ){ '.
							'window.pageLibraries = new Array(); '.
						'} '.
						'window.pageLibraries = window.pageLibraries.concat( '.
																	'new Array( '.
																		'new Array( '.
																			'"Matchmaking", '.
																			'"availableGameList" '.
																		') '.
																	') '.
																');'
						);
									
		return $returnString;
		
	}
	
	
	//PUBLIC FUNCTION: manageUnitsButton
	//Button to redirect to the user's unit management screen
	/**
	 * @return mixed
	 */
	public function manageUnitsButton(){
		
		return $this->Html->link( 
							'Manage Units',
						    [
						        'controller' => 'Units',
						        'action' => 'manageUnits'
						    ]
						);
		
	}
	
	//PUBLIC FUNCTION: pendingMatchBlurb
	//Create the pending match blurb
	/**
	 * @param $matchmakingQueueUID
	 * @return mixed
	 */
	public function pendingMatchBlurb( $matchmakingQueueUID ){

		//Temporary blurb
		$displayString = 'Waiting for match...';
						
		//Wrap it all in a div and toss it back
		$returnString = $this->Html->tag(
										'div',
										$displayString,
										[
											'class' => 'pendingMatchBlurb'
										]
									);
									
		return $returnString;
		
	}
	
	//PUBLIC FUNCTION: playGameButton
	//Create a container with an option to jump into gameplay
	/**
	 * @param $gameUID
	 * @return string
	 */
	public function playGameButton( $gameUID ){
		
		//Setup the return string
		$returnString = '';
		
		//For now just return a link		
		$returnString .= $this->Html->link( 
							'Play Game '.$gameUID,
						    [
						        'controller' 	=> 'Games',
						        'action' 		=> 'playGame',
								'?'				=> [
									'gameUID'		=> $gameUID
								]
						    ]
						);
						
		//Wrap it all in a div and toss it back
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										[
											'class' => 'playGameButton'
										]
									);
		return $returnString;
		
		
	}
	
	//PUBLIC FUNCTION: queueButton
	//Creates a button that can be clicked to join the matchmaking queue
	/**
	 * @return string
	 */
	public function queueButton(){
	
		//Grab the user's UID from the Auth component
		$userUID = AuthComponent::user('uid');
	
		//Get all of the teams associated with the user so that they can pick one before
		//joining the queue.
		$teamsModelInstance = ClassRegistry::init( 'Team' );
		$teams				= $teamsModelInstance->getTeamsByUserUID( $userUID );		
				
		//Setup the return string
		$returnString = '';
		
		//Add a model select button
		$returnString .= $this->TurnForm->modelSelect( 
										$teams,
										'name',
										[
											'class' => 'queueTeamSelect'
										]
									);
									
		//Add the actual button
		$returnString .= $this->Html->tag(
										'input',
										'',
										[
											'class' => 'joinQueueButton',
											'type'	=> 'button',
											'value'	=> 'Play Now'
										]
									);
									
		//Add a div to contain the time waited so far
		$returnString .= $this->Html->tag(
										'div',
										'',
										[
											'class' => 'matchmakingTimeWaited'
										]
									);
		
		//Wrap everything in a matchmakingButton div
		$returnString = $this->Html->tag(
										'div',
										$returnString,
										[
											'class' => 'matchmakingButton'
										]
									);	
									
		//Add in the libraries that are needed to make this part run
		$returnString .= $this->Html->tag(	
						'script',
						'if( typeof window.pageLibraries === "undefined" ){ '.
							'window.pageLibraries = new Array(); '.
						'} '.
						'window.pageLibraries = window.pageLibraries.concat( '.
																	'new Array( '.
																		'new Array( '.
																			'"Matchmaking", '.
																			'"joinQueue" '.
																		') '.
																	') '.
																');'
						);
		
		//Return the return string
		return $returnString;
		
	}
					

	
}