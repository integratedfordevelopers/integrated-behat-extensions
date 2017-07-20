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
 * @author Michael Jongman <michael@e-active.nl>
 */
trait Response
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Then /^the response should contain regex "(?P<text>(?:[^"]|\\")*)"$/
     *
     * @param string $regex
     * @thorws ExpectationException
     */
    public function responseShouldContainRegex($regex)
    {
        $actual = $this->getSession()->getPage()->getContent();
        $actual = preg_replace('/\s+/u', ' ', $actual);

        if (0 === preg_match(ReformatRegex::spaceTabsEqual(sprintf('/%s/', $regex)), $actual)) {
            // No matches meaning we havent found what were looking for
            throw new ExpectationException(
                sprintf(
                    'The response %s can not be found on %s',
                    $regex,
                    $this->getSession()->getCurrentUrl()
                ),
                $this->getSession()->getDriver()
            );
        }
    }
}
