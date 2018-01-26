<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Extensions\DOM;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait Element
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I should see an unique element with xpath "([\w\ \/\\\[\]\@\"=\-_]*)"$/
     *
     * @param string $xpath
     * @throws ExpectationException
     */
    public function iShouldSeeAnUniqueElementWithXpath($xpath)
    {
        $result = $this->getSession()->getPage()->find('xpath', $xpath);

        if (1 !== count($result)) {
            // The must be exacly on e element on the page, anything is not correct
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned %d elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl(),
                    count($result)
                ),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @Given /^I should see an element with xpath "([\w\ \/\\\[\]\@\"=\-_]*)"$/
     *
     * @param string $xpath
     * @throws ExpectationException
     */
    public function iShouldSeeAnElementWithXpath($xpath)
    {
        $result = $this->getSession()->getPage()->find('xpath', $xpath);

        if (1 > count($result)) {
            // The must be exacly on e element on the page, anything is not correct
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned %d elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl(),
                    count($result)
                ),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @Given /^I follow xpath "(.*)"$/
     *
     * @param string $xpath
     */
    public function iFollowXpath($xpath)
    {
        $result = $this->getSession()->getPage()->find('xpath', $xpath);

        if (1 !== count($result)) {
            // We did not find the link or we've found more than we want
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned %d elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl(),
                    count($result)
                ),
                $this->getSession()->getDriver()
            );
        }

        $result->click();
    }
}
