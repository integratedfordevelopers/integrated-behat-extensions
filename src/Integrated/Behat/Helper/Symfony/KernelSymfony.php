<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Helper\Symfony;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class KernelSymfony
{
    /**
     * @param Session $session
     * @return ContainerInterface
     * @throws \LogicException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public static function getContainer(Session $session)
    {
        $driver = $session->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException(
                'This step is only supported by the BrowserKitDriver (given: %s)',
                $driver
            );
        }

        /** @var \Symfony\Bundle\FrameworkBundle\Client $client */
        $client = $driver->getClient();
        if ($client instanceof Client) {
            return $client->getContainer();
        }

        // This should not happen
        throw new \LogicException(
            sprintf(
                'Client must be a %s but given was %s',
                Client::class,
                is_object($client) ? get_class($client) : gettype($client)
            )
        );
    }
}
