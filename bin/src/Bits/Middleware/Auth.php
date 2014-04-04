<?php
/**
 * Bits
 *
 * @copyright Mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

namespace Bits\Middleware;

use Slim\Middleware;
use Bits\Application;
use Zend\Authentication\Result;

/**
 * Authentication middleware
 *
 * Checks if user is authenticated when user visiting secured URI.
 * Will redirect a user to login if they attempt to visit a secured URI and are not authenticated
 */
class Auth extends Middleware
{
    /**
     * Bits application container (pimple)
     * @var Bits\Application
     */
    private $bits;

    /**
     * Public Constructor
     * @param Bits\Application $bits
     */
    public function __construct(Application $bits)
    {
        $this->bits = $bits;
    }

    /**
     * Uses 'slim.before.router' to check for authentication when visitor attempts
     * to access a secured URI. Will redirect unauthenticated user to login page.
     */
    public function call()
    {
        $app = $this->app;
        $bits = $this->bits;


        $checkAuth = function () use ($app, $bits) {
            $securedUrls = !empty($bits['configs']['app']['secured.urls']) ? $bits['configs']['app']['secured.urls'] : [];
            foreach ($securedUrls as $url) {
                $urlPattern = '@^' . $url . '$@';
                if (preg_match($urlPattern, $app->request()->getPathInfo()) === 1 && $bits['authenticationService']->hasIdentity() === false) {
                    return $app->redirect('/log-in');
                }
            }
        };

        $this->app->hook('slim.before.router', $checkAuth);

        $this->next->call();
    }


}