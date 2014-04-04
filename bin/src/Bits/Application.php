<?php
/**
 * Bits
 *
 * @copyright Mospired 2014
 * @author Moses Ngone <moses@mospired.com>
 */

namespace Bits;


use Pimple;
use Mandrill;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Cache\MemcacheCache;

use Zend\Cache\StorageFactory;
use Zend\Session\SessionManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\Container;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

/**
 * Bits Pimple
 */
class Application extends Pimple
{
    /**
     * Application
     *
     * Refrect on the class and appropriately define services/parameters
     * See http://pimple.sensiolabs.org/ for options
     */
    public function __construct()
    {
        parent::__construct();

        $reflection = new \ReflectionClass(__NAMESPACE__ . '\Application');

        $methods = $reflection -> getMethods();
        foreach ($methods as $method) {
            if (strpos($method -> name, '_init') !== false) {
                $method_name = $method -> name;
                $this -> $method_name();
            }
        }
    }

    /**
     * Return the application environemnt configuration options
     * @return Object
     */
    public function _initConfig()
    {
        $appEnv= APPLICATION_ENV;
        $config_file = BIN_PATH."/configs/{$appEnv}.json";

        $config_content= file_get_contents($config_file);
        $configs =json_decode($config_content,true);
        $this['configs']=$configs;
    }

    /**
     * Connect to MongoDb and return a doctrine document manager
     * See http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/introduction.html
     *
     * @return DocumentManager
     *
     */
    public function _initDb()
    {
        $container = $this;
        $this['documentManager'] =  $this->share(function () use ($container) {
            $dbConfigs = $container['configs']['database'];
            try{

                $connection_url = "mongodb://{$dbConfigs['host']}:{$dbConfigs['port']}/{$dbConfigs['name']}";

                AnnotationDriver::registerAnnotationClasses();

                /**
                 * setup Doctrine configuration
                 */
                $config = new Configuration();
                $config->setProxyDir(BIN_PATH.'/src/Bits/Documents/Proxies');
                $config->setProxyNamespace('Proxies');
                $config->setHydratorDir(BIN_PATH.'/src/Bits/Documents/Hydrators');
                $config->setHydratorNamespace('Hydrators');
                $config->setMetadataDriverImpl(AnnotationDriver::create(BIN_PATH.'/src/Bits/Documents'));
                $config->setDefaultDB($dbConfigs['name']);

                return DocumentManager::create( new Connection($connection_url), $config);

            }catch(Exception $e){
                error_log($e->getMessage());
            }
        });
    }  //

    /**
     * Initialize Mandrill email
     * See https://mandrillapp.com/api/docs/index.php.html
     *
     * @return Mandrill
     */
    public function _initPostman()
    {
        $container = $this;
        $this['postMan'] =  $this->share(function () use ($container) {
            $postmanConfigs = $container['configs']['services']['mandrill'];
            return new Mandrill($postmanConfigs['api_key']);
        });
    }

    /**
     * Setup File System Cache
     * See http://framework.zend.com/manual/2.0/en/modules/zend.cache.storage.adapter.html#the-filesystem-adapter
     *
     * @return Zend\Cache\Manager
     */
    public function _initCache()
    {
        $container = $this;
        $this['cacheManager'] =  $this->share(function () use ($container) {
            $cache  = StorageFactory::adapterFactory('filesystem', ['ttl' => 2419200,'cache_dir'=>CACHE_DIR.'/sessions']);
            $plugin = StorageFactory::pluginFactory('exception_handler',['throw_exceptions' => true]);
            $cache->addPlugin($plugin);
            return $cache;
        });
    }

    /**
     * Create a Session Manager using the cache manager as storage
     * See http://framework.zend.com/manual/2.1/en/modules/zend.session.manager.html
     *
     * @return Zend\Session\Manager
     */
    public function _initSessionManager()
    {
        $container = $this;

        $this['sessionManager'] = $this->share( function () use ($container){

            $sessionConfigs = $container['configs']['app']['sessions'];
            $saveHandler = new Cache($container['cacheManager']);
            $config = new SessionConfig();
            $config->setOptions($sessionConfigs);
            $manager= new SessionManager($config);
            $manager->setSaveHandler($saveHandler);
            Container::setDefaultManager($manager);
            return $manager;
        });
    }

    /**
     * Setup an authentication service
     * See http://framework.zend.com/manual/2.0/en/modules/zend.authentication.intro.html
     *
     * @return Zend\Authentication\AuthenticationService
     */
    public function _initAuthenticationService()
    {
        $container = $this;

        $this['authenticationService'] = $this->share( function () use ($container){
            $sessionStorage = new SessionStorage('Bits','storage', $container['sessionManager']);
            $auth= new AuthenticationService();
            return $auth->setStorage($sessionStorage);
        });
    }
}