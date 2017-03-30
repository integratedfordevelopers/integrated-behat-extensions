<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Extensions\Email;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

use Integrated\Behat\Helper\Symfony\KernelSymfony;

use Swift_Message;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait SymfonyEmail
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Then /^I must have received an email from "([^"]*)" with subject "([^"]*)"$/
     *
     * @param string $from
     * @param string $subject
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iMustHaveReceivedAnEmail($from, $subject)
    {
        // Get the container
        $container = KernelSymfony::getContainer($this->getSession());

        $filesystem = new Filesystem();
        if ($filesystem->exists($container->getParameter('swiftmailer.spool.default.file.path'))) {
            // Read the spool directory
            $finder = new Finder();
            $finder
                ->in($container->getParameter('swiftmailer.spool.default.file.path'))
                ->ignoreDotFiles(true)
                ->files();

            /** @var \SplFileInfo $file */
            foreach ($finder as $file) {
                /** @var Swift_Message $message */
                $message = unserialize(file_get_contents($file));

                if ($message->getFrom() === $from && $message->getSubject() === $subject) {
                    unlink($file->getPathname());
                }
            }
        }

        // We've failed our test
        throw new ExpectationException(
            sprintf(
                'No mail from %s with subject %s found.',
                $from,
                $subject
            ),
            $this->getSession()->getDriver()
        );
    }
}
