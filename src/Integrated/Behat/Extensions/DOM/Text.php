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

use Integrated\Behat\Helper\Regex\ReformatRegex;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait Text
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I should see regex "(.*)"$/
     *
     * @param string $regex
     * @thorws ExpectationException
     */
    public function iShouldSeeRegex($regex)
    {
        $actual = $this->getSession()->getPage()->getText();
        $actual = preg_replace('/\s+/u', ' ', $actual);
        
        if (0 === preg_match(ReformatRegex::spaceTabsEqual(sprintf('/%s/', $regex)), $actual)) {
            // No matches meaning we havent found what were looking for
            throw new ExpectationException(
                sprintf(
                    'The content %s can not be found on %s',
                    $regex,
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @Given /^I should not see regex "(.*)"$/
     *
     * @param string $regex
     * @thorws ExpectationException
     */
    public function iShouldNotSeeRegex($regex)
    {
        if (0 !== preg_match(ReformatRegex::spaceTabsEqual(sprintf('/%s/', $regex)), trim(strip_tags($this->getSession()->getPage()->getContent())))) {
            // Matches meaning we have found what were looking for
            throw new ExpectationException(
                sprintf(
                    'The content %s should not be found on %s',
                    $regex,
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * @Given /^I should see in xpath "(.*)" regex "(.*)"$/
     *
     * @param string $xpath
     * @param string $regex
     * @thorws ExpectationException
     */
    public function iShouldSeeTextInXpath($xpath, $regex)
    {
        $result = $this->getSession()->getPage()->find('xpath', $xpath);

        if (1 !== count($result)) {
            // We should only have one element
            throw new ExpectationException(
                sprintf(
                    'The xpath %s returned %d results on %s',
                    $xpath,
                    is_null($result) ? 0 : count($result),
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }

        if (0 === preg_match(ReformatRegex::spaceTabsEqual(sprintf('/%s/', $regex)), trim(strip_tags($result->getHtml())))) {
            // Text not found
            throw new ExpectationException(
                sprintf(
                    'The content %s can not be found on %s in element %s',
                    $xpath,
                    $this->getSession()->getCurrentUrl(),
                    $regex
                ),
                $this->getSession()->getDriver()
            );
        }
    }
}
