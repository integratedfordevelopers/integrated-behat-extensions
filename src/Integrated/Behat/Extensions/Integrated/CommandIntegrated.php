<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Extensions\Integrated;

use Behat\Mink\Session;

use Doctrine\DBAL\Connection;

use Integrated\Behat\Helper\Symfony\KernelSymfony;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
trait CommandIntegrated
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @When /^I run "([^"]*)" command$/
     */
    public function iRunCommand($name)
    {
        $application = new Application(KernelSymfony::getContainer($this->getSession())->get('kernel'));
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $name,
            '--env' => 'prod'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
    }
}
