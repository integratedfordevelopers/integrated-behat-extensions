<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Extensions\Login;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait SymfonyLogin
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I am authenticated as "([^\s]|[^\ ]*)" in firewall "([^"]+)"$/
     *
     * @param string $username
     * @param string $firewall
     * @throws UnsupportedDriverActionException
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function iAmAuthenticatedAsInFirewall($username, $firewall)
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException(
                'This step is only supported by the BrowserKitDriver (given: $s)',
                $driver
            );
        }

        /** @var \Symfony\Bundle\FrameworkBundle\Client $client */
        $client = $driver->getClient();
        $client->getCookieJar()->set(new Cookie(session_name(), true));

        if ($user = $client->getContainer()->get('integrated_user.user.manager')->findByUsername($username)) {
            $session = $client->getContainer()->get('session');

            $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
            $session->set(sprintf('_security_%s', $firewall), serialize($token));
            $session->save();

            $cookie = new Cookie($session->getName(), $session->getId());
            $client->getCookieJar()->set($cookie);
        } else {
            throw new UsernameNotFoundException(
                sprintf('The requested user %s is not found', $username)
            );
        }
    }
}
