<?php

if( $authUser == null or $authUser['User']['username'] == null ){

    echo '<div class="loginForm">';

    echo '<label for="loginUsernamePrompt">Username:</label>';
    echo '<input type="text" id="loginUsernamePrompt" />';

    echo '<label for="loginUsernamePrompt">Password:</label>';
    echo '<input type="password" id="loginPasswordPrompt" />';
    echo '<input type="button" value="Login" id="loginButton">';
    echo '</div>';

}else{

    echo '<div class="loggedInUser">'.$authUser['User']['username'].'</div>';

}

?>