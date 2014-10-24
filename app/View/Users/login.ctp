<?php

if( $authUser == null or $authUser['User']['username'] == null ){

    echo '<div class="loginForm">
	    <label for="loginUsernamePrompt">Username:</label>
	    <input type="text" id="loginUsernamePrompt" />

	    <label for="loginUsernamePrompt">Password:</label>
	    <input type="password" id="loginPasswordPrompt" />
	    <input type="button" value="Login" id="loginButton">
    </div>';

}else{

    echo '<div class="loggedInUser">'.$authUser['User']['username'].'</div>';

}