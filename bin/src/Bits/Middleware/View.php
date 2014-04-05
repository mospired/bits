<?php
/**
 * Bits
 *
 * @copyright Mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

namespace Bits\Middleware;

use Slim\Middleware;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;

/**
 * View Middleware
 *
 * Instantiate view
 */
class View extends Middleware
{
    public function call()
    {
        $configs = [
        'view'=> new Twig(),
        'templates.path'=> APP_PATH.'/templates/views'
        ];
        $this->app->config($configs);

        // set Twig Extensions
        $this->app->view->parserOptions = ['debug' => true,'cache' => CACHE_DIR.'/ui'];
        $this->app->view->parserExtensions = [ new TwigExtension(),new Twig_Extension_Debug()];
        $this->next->call();

    }
}