<?php
/**
 * Bits
 *
 * @copyright mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

/**
 * Profile Route
 */
$app->get('/profile',function () use ($app,$bits) {

    $memberQr= $bits['documentManager']->getRepository('\Bits\Documents\Member');
    $member = $memberQr->find($bits['authenticationService']->getIdentity());

    $view = [
    'route' => 'profile',
    'member'=>$member->toArray(),
    ];


    $app->render("profile.html", $view);
    $app->response->headers->set('Content-Type', 'text/html');

});