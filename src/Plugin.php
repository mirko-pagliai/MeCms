<?php
declare(strict_types=1);

/**
 * This file is part of me-cms.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-cms
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @since       2.24.0
 */

namespace MeCms;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Utility\Inflector;
use MeCms\Command\Install\RunAllCommand;
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\Command\Install\SetPermissionsCommand;
use MeTools\Plugin as MeTools;
use RecaptchaMailhide\Plugin as RecaptchaMailhide;
use StopSpam\Plugin as StopSpam;
use Symfony\Component\Finder\Finder;
use Thumber\Cake\Plugin as Thumber;
use Tokens\Plugin as Tokens;

/**
 * Plugin class
 */
class Plugin extends BasePlugin
{
    /**
     * Returns `true` if is cli.
     * @return bool
     */
    protected function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Load all the application configuration and bootstrap logic
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        $pluginsToLoad = [
            MeTools::class,
            RecaptchaMailhide::class,
            StopSpam::class,
            Thumber::class,
            Tokens::class,
        ];
        if (getConfig('default.theme')) {
            $pluginsToLoad[] = getConfig('default.theme');
        }
        if (!$this->isCli()) {
            $pluginsToLoad[] = 'WyriHaximus/MinifyHtml';
        }

        foreach ($pluginsToLoad as $plugin) {
            /** @var \Cake\Http\BaseApplication $app */
            if (!$app->getPlugins()->has($plugin)) {
                //Initializes bootstrapped plugins
                if (method_exists($plugin, 'bootstrap')) {
					/** @var \Cake\Core\PluginInterface $plugin */
                    $plugin = new $plugin();
                    $plugin->bootstrap($app);
                }

                $app->addPlugin($plugin);
            }
        }

        parent::bootstrap($app);

        if (!$this->isCli()) {
            //Loads DebugKit, if debugging is enabled
            if (getConfig('debug') && !$app->getPlugins()->has('DebugKit')) {
                $app->addOptionalPlugin('DebugKit');
            }
        }
    }

    /**
     * Add console commands for the plugin
     * @param \Cake\Console\CommandCollection $commands The command collection to update
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        //Auto-discovers all `MeCms` commands.
        //Unlike the `CommandCollection::discoverPlugin()` method, it also finds installation commands
        $files = Finder::create()->files()->name('/Command\.php$/')->in($this->getPath() . 'src' . DS . 'Command');
        foreach ($files as $fileInfo) {
            $className = 'MeCms\\' . str_replace('/', '\\', substr($fileInfo->getPath(), strlen($this->getPath() . 'src' . DS))) . '\\' . $fileInfo->getBasename('.php');
            $name = Inflector::underscore(preg_replace('/Command\.php$/', '', $fileInfo->getFilename()));
            $commands->add('me_cms.' . $name, $className);
        }

        //Renames `RunAllCommand` command
        $commands->add('me_cms.install', RunAllCommand::class);

        //Adds commands from MeTools
        return $commands->add('me_cms.create_directories', CreateDirectoriesCommand::class)
            ->add('me_cms.create_vendors_links', CreateVendorsLinksCommand::class)
            ->add('me_cms.set_permissions', SetPermissionsCommand::class);
    }

    /**
     * Adds middleware for the plugin
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to update
     * @return \Cake\Http\MiddlewareQueue
     * @since 2.26.4
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $key = Configure::read('Security.cookieKey', md5(Configure::read('Security.salt', '')));

        return $middlewareQueue->add(new EncryptedCookieMiddleware(['login'], $key));
    }
}
