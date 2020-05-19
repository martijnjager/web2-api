<?php

function dump(...$args)
{
    echo '<pre>';
    var_dump($args);
    echo '</pre>';
}

function dd(...$args)
{
    echo '<pre>';
    var_dump($args);
    echo '</pre>';
    die(1);
}

function null(...$args)
{
    return is_null($args);
}
