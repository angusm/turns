// JavaScript Document

//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all
var Authentication_authentication = null;

var loadDependenciesFor_Authentication_authentication = function(){

    libraries.push( new Array( 'Game', 'elements' ) );

}

//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){

    Authentication_authentication = new Authentication();
    Authentication_authentication.handleEverything();

});

//Alright let's do this matchmaking stuff
var Authentication = function(){

    //PUBLIC FUNCTION: handleEverything
    //Set everything up
    this.handleEverything = function(){

        Authentication_authentication.setupLoginButton();

    }

    //PUBLIC FUNCTION: setupLoginButton
    //Attach the necessary listeners to the login button
    this.setupLoginButton = function(){

        //Grab the login information
        jQuery( 'input[type="button"]#loginButton' ).click( function(){

            //Grab the username and password
            var password = jQuery( 'input[type="password"]#loginPasswordPrompt').val();
            var username = jQuery( 'input[type="text"]#loginUsernamePrompt').val();

            //Then post it to the server and return the resulting info if its valid
            jQuery.getJSON(
                homeURL + 'Users/processLogin',
                {
                    password: password,
                    username: username
                },
                function( jSONData ){

                    if( jSONData.success == true ){
                        alert( 'Login successful' );
                    }else{
                        alert( 'Unable to log in' );
                    }

                }
            ).done(
                function(){
                }
            ).fail(
                function(data){
                }
            ).always(
                function(){
                }
            );


        });

    }

}