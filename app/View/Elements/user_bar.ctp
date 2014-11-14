<?php

echo '<div class="userBar">';

if( $authUser == null or $authUser['username'] == null or $authUser['uid'] == null ){

    echo '<div class="loginForm">';

    echo '<input type="button" value="Login" id="loginButton" class="loginFormElement">';

    echo '<label class="loginFormElement" for="loginUsernamePrompt">Username</label>';
    echo '<input type="text" id="loginUsernamePrompt" class="loginFormElement" />';

    echo '<label class="loginFormElement" for="loginUsernamePrompt">Password</label>';
    echo '<input type="password" id="loginPasswordPrompt" class="loginFormElement" />';

    echo '</div>';

}else{

    echo '<div class="loggedInUser loginFormElement">'.$authUser['username'].'</div>';

}

echo '</div>';

