<?php
/**
 * Bits
 *
 * @copyright mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */


/**
 * Home Route
 */
$app->get('/',function() use ($app,$bits){

    if($bits['authenticationService']->hasIdentity()){
        $app->redirect('/profile/');
    }

    $app->render('home.html');
    $app->response->headers->set('Content-Type', 'text/html');
});