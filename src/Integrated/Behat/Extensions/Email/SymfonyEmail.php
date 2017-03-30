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

            $file = null;

            /** @var \SplFileInfo $file */
            foreach ($finder as $spoolFile) {
                /** @var Swift_Message $message */
                $message = unserialize(file_get_contents($spoolFile));

                $sender = $message->getFrom();
                if (isset($sender[$from]) && $message->getSubject() === $subject) {
                    // Keep the mail to remove it
                    $file = $spoolFile;

                    // Get out the loop
                    break;
                }
            }

            if ($file) {
                // Remove the email, it has been asserted
                unlink($file->getPathname());
            } else {
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
        } else {
            // We've failed our test
            throw new ExpectationException(
                'There is no spooler directory, did you add the configuration from the README.md in the config_test.yml?',
                $this->getSession()->getDriver()
            );
        }
    }
}
