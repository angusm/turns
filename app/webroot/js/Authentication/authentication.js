
//Alright let's handle authentication
var Authentication = function(){
};

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
                    if( 13 === e.keyCode ){
                        e.preventDefault();
                        jQuery('input[type="password"]#loginPasswordPrompt').focus();
                    }
                });

            //When enter is hit inside the password field, trigger the login button
            jQuery(this).on(
                'keydown.authentication',
                'input[type="password"]#loginPasswordPrompt',
                function(e){
                    if( 13 === e.keyCode ){
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
                        window.Paths.webroot + 'Users/processLogin',
                        {
                            password: password,
                            username: username
                        },
                        function( jSONData ){
                            if( true === jSONData.success ){
                                document.location.href = (window.Paths.webroot + jSONData['redirectURL']).replace('//', '/');
                            }else{
                                alert( 'Unable to log in' );
                            }
                        }
                    );
                });

        });

    }

};