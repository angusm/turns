<?php

//Toss up the extra libraries
echo $this->Html->tag(
    'script',
    'window.pageData = {
        User: {
            uid: '.$userUID.'
        },
        Game: {
            uid: '.$gameUID.',
            currentTurn: 0
        }
    };'
);