<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Routing\DispatcherFactory;

require_once 'vendor/autoload.php';

define('ROOT', dirname(__DIR__) . DS);
define('ROOT_CM', ROOT . 'vendor' . DS . 'cakemanager' . DS . 'cakephp-cakemanager' . DS);
define('ROOT_UTILS', ROOT . 'vendor' . DS . 'cakemanager' . DS . 'cakephp-utils' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('APP', ROOT . 'tests' . DS . 'App' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CACHE', TMP);
define('LOGS', TMP);
define('CONFIG', APP . 'config' . DS);

require CAKE . 'Core/ClassLoader.php';

$loader = new Cake\Core\ClassLoader;
$loader->register();

require_once CORE_PATH . 'config' . DS . 'bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => 'src',
    'webroot' => 'webroot',
    'www_root' => APP . 'webroot',
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [APP . 'Plugin' . DS],
        'templates' => [APP . 'Template' . DS]
    ]
]);

Cache::config([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true
    ]
]);

// Ensure default test connection is defined
if (!getenv('db_class')) {
    putenv('db_class=Cake\Database\Driver\Sqlite');
    putenv('db_dsn=sqlite::memory:');
}

ConnectionManager::config('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => getenv('db_class'),
    'dsn' => getenv('db_dsn'),
    'database' => getenv('db_database'),
    'username' => getenv('db_login'),
    'password' => getenv('db_password'),
    'timezone' => 'UTC'
]);

Configure::write('Session', [
    'defaults' => 'php'
]);

Log::config([
    'debug' => [
        'engine' => 'Cake\Log\Engine\FileLog',
        'levels' => ['notice', 'info', 'debug'],
        'file' => 'debug',
    ],
    'error' => [
        'engine' => 'Cake\Log\Engine\FileLog',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'file' => 'error',
    ]
]);

Log::config('queries', [
    'className' => 'Console',
    'stream' => 'php://stderr',
    'scopes' => ['queriesLog']
]);


Plugin::load('Utils', ['path' => ROOT_UTILS]);
Plugin::load('CakeManager', ['path' => ROOT_CM, 'bootstrap' => true, 'routes' => true]);
Plugin::load('PostTypes', ['path' => ROOT, 'bootstrap' => true, 'routes' => true]);

Carbon\Carbon::setTestNow(Carbon\Carbon::now());

DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');