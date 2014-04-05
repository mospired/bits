<?php
/**
 * Bits
 *
 * @copyright mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

use Bits\Authentication\Adapter as AuthAdapter;
use Zend\Authentication\Result;

/**
 * Display Login form
 */
$app->get('/login/',function() use ($app,$bits){

    if($bits['authenticationService']->hasIdentity()){
        $app->redirect('/profile');
    }

    $app->render("login.html", array('route' => 'login'));
    $app->response->headers->set('Content-Type', 'text/html');
});



/**
 * Process Login form
 */
$app->post('/login/',function() use ($app,$bits){

    $email = filter_var($app->request()->post('email'), FILTER_SANITIZE_EMAIL);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)){

        $app->response()->status(403);
        $app->flash('errors', ['Invalid email address']);
        $app->redirect("/sign-in");
    }

    $password = $app->request()->post('password');


    $memberQr= $mapster['documentManager']->getRepository('\Mapster\Documents\Member');

    $authAdapter = new AuthAdapter($memberQr,$email,$password);

    $result = $mapster['authenticationService']->authenticate($authAdapter);


    switch ($result->getCode()) {

        case Result::SUCCESS:
        $app->redirect('/profile');
        break;

        default:
        $app->response()->status(403);
        $app->flash('errors', $result->getMessages());
        $app->redirect('/login/');
        break;
    }

});

