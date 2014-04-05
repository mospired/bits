<?php
/**
 * Bits
 *
 * @copyright mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

// set constants
define('APPLICATION_ENV','development');
defined('APPLICATION_ROOT') || define('APPLICATION_ROOT', realpath(dirname(__FILE__).'/../'));
defined('APP_PATH') || define('APP_PATH', APPLICATION_ROOT.'/app');
defined('BIN_PATH') || define('BIN_PATH', APPLICATION_ROOT.'/bin');
defined('CACHE_DIR') || define('CACHE_DIR', APPLICATION_ROOT.'/cache');

require APPLICATION_ROOT.'/vendor/autoload.php';


use Slim\Slim;
use Slim\Views;
use Slim\Middleware\SessionCookie;
use Bits\Application;
use Bits\Middleware\Auth;
use Bits\Middleware\View;

// instantiate my application (pimple)
$bits = new Application();

$app = new Slim($bits['configs']['app']);
$app->setName('bits');
$app->add(new View());
$app->add(new Auth($bits));
$app->add(new SessionCookie($bits['configs']['app']['cookies']));

require APP_PATH."/init.php";

$app->run();