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
    protected $allowedChannels = [
        'solr-indexer',
        'statistic_year_rating',
        'published_changed_queue',
        'score',
        'disease-score',
    ];

    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Then /^I wait until background processes are done$/
     */
    public function clearBackgroundProcesses($try = 1, $tries = 20)
    {
        $queueChannels = $this->getQueueChannels();

        if (!$channels = array_intersect($queueChannels, $this->allowedChannels)) {
            return true;
        }

        foreach ($channels as $channel) {
            $this->clearQueue($channel['channel']);
        }

        if ($try < $tries) {
            $this->clearBackgroundProcesses(++$try);
        }

        return false;
    }

    /**
     * @param string $channel
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearQueue($channel)
    {
        if ('solr-indexer' === $channel) {
            $this->clearSolrQueue();
        }
        if ('statistic_year_rating' === $channel) {
            $this->clearStatisticYearQueue();
        }
        if ('published_changed_queue' === $channel) {
            $this->clearPublishedQueue();
        }
        if ('score' === $channel) {
            $this->clearScoreQueue();
        }
        if ('disease-score' === $channel) {
            $this->clearDiseaseScoreQueue();
        }
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearSolrQueue()
    {
        $input = new ArrayInput([
            'command' => 'solr:indexer:run',
            '--blocking' => true,
        ]);
        
        $this->runCommand($input);
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearStatisticYearQueue()
    {
        $input = new ArrayInput([
            'command' => 'statistic:year:calculate-rating'
        ]);

        $this->runCommand($input);
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearPublishedQueue()
    {
        $input = new ArrayInput([
            'command' => 'zkn:published:update',
            '--dequeue' => true
        ]);

        $this->runCommand($input);
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearScoreQueue()
    {
        $input = new ArrayInput([
            'command' => 'zkn:calculate:score'
        ]);

        $this->runCommand($input);
    }

    /**
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function clearDiseaseScoreQueue()
    {
        $input = new ArrayInput([
            'command' => 'zkn:calculate:disease-score'
        ]);

        $this->runCommand($input);
    }

    /**
     * @param ArrayInput $input
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    protected function runCommand(ArrayInput $input)
    {
        $application = new Application(KernelSymfony::getContainer($this->getSession())->get('kernel'));
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    /**
     * @return array
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getQueueChannels()
    {
        return $this->getConnection()->query('SELECT channel FROM queue GROUP BY channel')->fetchAll();
    }

    /**
     * @return Connection|object
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    private function getConnection()
    {
        $container = KernelSymfony::getContainer($this->getSession());

        return $container->get('doctrine.dbal.default_connection');
    }
}
