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
 * @since       2.31.7
 */

namespace MeCms\Controller\Component;

use Authentication\Authenticator\AuthenticatorInterface;
use Authentication\Controller\Component\AuthenticationComponent as BaseAuthenticationComponent;
use Authorization\Identity;
use Cake\Event\EventInterface;

/**
 * Controller Component for interacting with Authentication.
 *
 * This class just adds the `afterIdentify` event.
 */
class AuthenticationComponent extends BaseAuthenticationComponent
{
    /**
     * Get the Controller callbacks this Component is interested in
     * @return array<string, mixed>
     */
    public function implementedEvents(): array
    {
        return ['Authentication.afterIdentify' => 'afterIdentify'] + parent::implementedEvents();
    }

    /**
     * `afterIdentify` event.
     *
     * Checks if the user is banned or if is disabled.
     * Then, it uses the `LoginRecorder` component to write the login as a log.
     * @param \Cake\Event\EventInterface $Event Event
     * @param \Authentication\Authenticator\AuthenticatorInterface $Provider Provider
     * @param \Authorization\Identity $Identity Identity
     * @return \Cake\Http\Response|void
     */
    public function afterIdentify(EventInterface $Event, AuthenticatorInterface $Provider, Identity $Identity)
    {
        /** @var \MeCms\Controller\AppController $Controller */
        $Controller = $this->getController();

        /** @var \MeCms\Model\Entity\User $User */
        $User = $Identity->getOriginalData();

        //Checks if the user is banned or if is disabled (the account should still be enabled)
        if ($User->get('banned') || !$User->get('active')) {
            $Controller->Flash->error($User->get('banned') ? __d('me_cms', 'Your account has been banned by an admin') : __d('me_cms', 'Your account has not been activated yet'));

            return $Controller->redirect($this->logout());
        }

        $Controller->LoginRecorder->setConfig('user', $User->get('id'));
        $Controller->LoginRecorder->write();
    }
}