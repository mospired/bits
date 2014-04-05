<?php
/**
 * Bits
 *
 * @copyright Mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

use Bits\Documents\Member;


/**
 * Signup form
 */
$app->get('/signup/',function() use ($app,$bits){

    if($bits['authenticationService']->hasIdentity()){
        $app->redirect('/profile');
    }

    $app->render("signup.html", ['route' => 'signup']);
    $app->response->headers->set('Content-Type', 'text/html');
});

/**
 * Process Signup
 */
$app->post('/signup/',function() use ($app,$bits){

    $params = $app->request->post();
    $email = filter_var($params['email'], FILTER_SANITIZE_EMAIL);


    $member = new Member;
    $member->email = $email;
    $member->password = password_hash($params['password'], PASSWORD_BCRYPT);

    $bits['documentManager']->persist($member);
    $bits['documentManager']->flush();

    $app->response->headers->set('Content-Type', 'text/html');
});
