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
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
trait BackgroundIntegrated
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Then /^I wait until background processes are cleared$/
     */
    public function clearBackgroundProcesses()
    {
        $queueChannels = $this->getQueueChannels();

        foreach ($queueChannels as $channel) {
            $this->clearQueue($channel['channel']);
        }
    }

    /**
     * @param String $channel
     */
    protected function clearQueue($channel)
    {
        if ('solr-indexer' === $channel) {
            $this->clearSolrQueue();
        }
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearSolrQueue()
    {
        $application = new Application(KernelSymfony::getContainer($this->getSession())->get('kernel'));
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'solr:indexer:run',
            '--blocking' => true,
            '--env' => 'prod'
        ));
        
        $output = new BufferedOutput();
        $application->run($input, $output);

        dump($output->fetch());
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getQueueChannels()
    {
        return $this->getConnection()->query('SELECT channel FROM queue GROUP BY channel')->fetchAll();
    }

    /**
     * @return Connection
     */
    private function getConnection()
    {
        $container = KernelSymfony::getContainer($this->getSession());

        return $container->get('doctrine.dbal.default_connection');
    }
}
