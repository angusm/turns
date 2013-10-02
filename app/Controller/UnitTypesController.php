<?php
// app/Controller/UsersController.php
class UnitTypesController extends AppController {

	//Setups the stuff that should happen before
	//any other action is called
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('');
    }

	//PUBLIC FUNCTION: index
	//Essentially a homepage for the users where they can
	//view all their lovely stuff.
    public function index() {
		//Empty... for now!		
    }
	
	//PUBLIC FUNCTION: add
	//Sets up the page where a user can register 
    public function add() {
		
		//If we're dealing with a posted message
        if ($this->request->is('post')) {
			
			//Create the user
            $this->UnitType->create();
			
			//See if we can save the user using the given data...
			if ($this->UnitType->save($this->request->data)) {
				//If the user has been saved, indicate as much and do a
				//redirect.
				$this->Session->setFlash(__('Unit Type Saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				//If the user couldn't be saved then indicate as much.
				$this->Session->setFlash(__('Couldn\'t save that unit type, it\'s dead now.'));
			}
        }
    }
	
	//PROTECTED FUNCTION: cardView
	//Display the card for the given unit type.
    protected function cardView( $request ) {
		
		//Get the Unit Type
		$unitType = $this->UnitType->getCardViewData(
								$request->data['UnitType']['UID'] 
							);
							
		print_r( $unitType );
							
		//Grab the other variables we need so we're not
		//doing a bunch of processing in the view
		
		//Get the defense value
		if( isset( $unitType['UnitStat']['defense'] ) ){
			$defenseValue = intval( $unitType['UnitStat']['defense'] );
		}else{
			$defenseValue = 0;
		}
		
		//Get the attack value
		if( isset( $unitType['UnitStat']['damage'] ) ){
			$attackValue = intval( $unitType['UnitStat']['damage'] );
		}else{
			$attackValue = 0;
		}
		
		//Get the name
		if( isset( $unitType['UnitType']['name'] ) ){
			$unitTypeName = $unitType['UnitType']['name'];
		}else{
			$unitTypeName = '';
		}
		
		//Get the teamcost
		if( isset( $unitType['UnitStat']['teamcost'] ) ){
			$teamcostValue = intval( $unitType['UnitStat']['teamcost'] );
		}else{
			$teamcostValue = '';
		}
		
		//Get the Unit's movement set if at all possible
		if( isset( $unitType['UnitStat']['UnitStatMovementSet'] ) ){
			$unitTypeMovementSets = $unitType['UnitStat']['UnitStatMovementSet'];
		}else{
			$unitTypeMovementSets = array();
		}
			
		//Get the Unit Art Set if at all possible
		if( isset( $unitType['UnitArtSet'][0] ) ){
			$unitArtSet = $unitType['UnitArtSet'][0];
			
			//Get a Card Art Layer if at all possible
			if( isset( $unitArtSet['CardArtLayerSet'] ) ){
				$cardArtLayerSets = $unitArtSet['CardArtLayerSet'];
			}else{
				$cardArtLayerSets = array();
			}
						
			//Get the art set icon
			foreach( $unitArtSet['UnitArtSetIcon'] as $unitArtSetIcon ){
			
				//Look for and find the Icon with position 7, the defense
				//icon position
				switch( $unitArtSetIcon['Icon']['icon_positions_uid'] ){
					case 3:
						$boardIcon					= $unitArtSetIcon['Icon']['image'];
						break;
					case 4:
						$attackIcon					= $unitArtSetIcon['Icon']['image'];
						break;
					case 5:
						$defenseIcon				= $unitArtSetIcon['Icon']['image'];
						break;
					case 6:
						$attackPointIcon			= $unitArtSetIcon['Icon']['image'];
						break;
					case 7:
						$defensePointIcon			= $unitArtSetIcon['Icon']['image'];
						break;
					case 8:
						$movementBoxIcon			= $unitArtSetIcon['Icon']['image'];
						break;
					case 9:
						$teamcostPointIcon			= $unitArtSetIcon['Icon']['image'];
						break;
					case 10:
						$movementArrowIcon			= $unitArtSetIcon['Icon']['image'];
						break;
					case 11:
						$movementSetSelectorIcon    = $unitArtSetIcon['Icon']['image'];
						break;
					case 12:
						$teamcostIcon				= $unitArtSetIcon['Icon']['image'];
						break;
				}
				
			}
			
		
		//If we can't get an Art Set setup default variables
		}else{
			$unitArtSet 				= array();
			$cardArtLayerSets 			= array();
			$boardIcon					= '';
			$attackIcon 				= '';
			$defenseIcon 				= '';
			$attackPointIcon 			= '';
			$defensePointIcon 			= '';
			$movementBoxIcon			= '';
			$teamcostPointIcon			= '';
			$movementArrowIcon 			= '';
			$movementSetSelectorIcon    = '';
			$teamcostIcon				= '';
		}
		
		//Set the view's variables
		$this->set( 'unitType', 				$unitType );
		$this->set( 'unitArtSet',				$unitArtSet );
		$this->set( 'cardArtLayerSets',			$cardArtLayerSets );
		$this->set( 'boardIcon',				$boardIcon );
		$this->set( 'attackIcon',				$attackIcon );
		$this->set( 'defenseIcon',				$defenseIcon );
		$this->set( 'attackPointIcon',			$attackPointIcon );
		$this->set( 'defensePointIcon',			$defensePointIcon );
		$this->set( 'movementBoxIcon',			$movementBoxIcon );
		$this->set( 'teamcostPointIcon',		$teamcostPointIcon );
		$this->set( 'movementArrowIcon',		$movementArrowIcon );
		$this->set( 'movementSetSelectorIcon',  $movementSetSelectorIcon );
		$this->set( 'teamcostIcon',				$teamcostIcon );
		$this->set( 'defenseValue',				$defenseValue );
		$this->set( 'attackValue',				$attackValue );
		$this->set( 'teamcostValue',			$teamcostValue );
		$this->set( 'unitTypeName',				$unitTypeName );
		$this->set( 'unitTypeMovementSets',		$unitTypeMovementSets );
		
		$this->render('cardView');
		
    }
	
	//PUBLIC FUNCTION: getCardView
	//Redirect to cardView on a post, display a selection box
	//if request isn't a post
	public function getCardView(){
				
		//If we're dealing with a posted message
		if ($this->request->is('post')) {
			//Call the function we really need
			$this->cardView( $this->request );
		//If we're not dealing with a posted message
		//then just display the view with the select
		}else{
			$this->set( 'uids', $this->UnitType->getUIDs() );
			$this->render('getCardView');
		}
		
	}
	
	
}