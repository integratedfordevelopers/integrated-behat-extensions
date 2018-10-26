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

        if (is_null($result)) {
            // There must be exactly one element on the page, anything else is not correct
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned no elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl()
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

        if (is_null($result)) {
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned no elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @Given /^I follow xpath "(.*)"$/
     *
     * @param string $xpath
     *
     * @throws ExpectationException
     */
    public function iFollowXpath($xpath)
    {
        $result = $this->getSession()->getPage()->find('xpath', $xpath);

        if (is_null($result)) {
            // We did not find the link or we've found more than we want
            throw new ExpectationException(
                sprintf(
                    'The xpath %s (on %s) returned no elements',
                    $xpath,
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }

        $result->click();
    }

    /**
     * @Then /^I click on "([^"]*)"$/
     *
     * @param string $element
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iClickOn($element)
    {
        $page = $this->getSession()->getPage();
        $findName = $page->find("css", $element);

        if (!$findName) {
            throw new ExpectationException(
                $element . " could not be found",
                $this->getSession()->getDriver()
            );
        }

        $findName->click();
    }
}
