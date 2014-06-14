// JavaScript Document

<<<<<<< HEAD
=======
//PEPPER POTTS FUNCTION: handleEverything
//Why? Because Pepper Potts is a badass bitch who just takes care of it all
var Authentication_authentication = null;

var loadDependenciesFor_Authentication_authentication = function(){
}

>>>>>>> origin/master
//DOCUMENT READY
//When the document is fully ready, call the main function
jQuery(document).ready( function(){
    var auth = new Authentication();
    auth.handleEverything();
});


//Alright let's handle authentication
var Authentication = function(){
}

Authentication.prototype = {
    //PUBLIC FUNCTION: handleEverything
    //Set everything up
    handleEverything:function(){
        this.setupLoginButton();
    },

    //PUBLIC FUNCTION: setupLoginButton
    //Attach the necessary listeners to the login button
    setupLoginButton:function(){

        //Add the various listeners required for this functionality
        jQuery(document).each( function(){

            //When ENTER is hit inside the username textbox change focus
            //to the password prompt
            jQuery(this).on(
               'keydown.authentication',
               'input[type="text"]#loginUsernamePrompt',
                function(e){
                    if(e.keyCode == 13){
                        e.preventDefault();
                        jQuery('input[type="password"]#loginPasswordPrompt').focus();
                    }
                });

            //When enter is hit inside the password field, trigger the login button
            jQuery(this).on(
                'keydown.authentication',
                'input[type="password"]#loginPasswordPrompt',
                function(e){
                    if(e.keyCode == 13){
                        e.preventDefault();
                        jQuery('input[type="button"]#loginButton').click();
                    }
                });

            //Handle grabbing the login information when the button is clicked
            jQuery(this).on(
                'click.authentication',
                'input[type="button"]#loginButton',
                function(){
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
                                document.location.href = (homeURL + jSONData['redirectURL']).replace('//', '/');
                            }else{
                                alert( 'Unable to log in' );
                            }
                        }
                    );
                });

        });

    }

}