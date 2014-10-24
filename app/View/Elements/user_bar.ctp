<?php

echo '<div id="userBar">';

if( $authUser == null or $authUser['username'] == null or $authUser['uid'] == null ){

    echo '<div class="loginForm">';

    echo '<div class="loginButton">';
    echo '<input type="button" value="Login" id="loginButton">';
    echo '</div>';

    echo '<div class="usernamePrompt">';
    echo '<label for="loginUsernamePrompt">Username</label>';
    echo '<input type="text" id="loginUsernamePrompt" />';
    echo '</div>';

    echo '<div class="passwordPrompt">';
    echo '<label for="loginUsernamePrompt">Password</label>';
    echo '<input type="password" id="loginPasswordPrompt" />';
    echo '</div>';

    echo '</div>';

}else{

    echo '<div class="loggedInUser">'.$authUser['username'].'</div>';

}

echo '</div>';

