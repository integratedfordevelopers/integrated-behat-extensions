<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Extensions\Form;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait Checkbox
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I check "([^"]*)" with value "([^"]*)"$/
     *
     * @param string $name
     * @param string $value
     * @throws ExpectationException
     */
    public function checkWithValue($name, $value)
    {
        /** @var \Behat\Mink\Element\NodeElement $nodeElement */
        $nodeElement = $this->getSession()->getPage()->find(
            'xpath',
            sprintf(
                '//input[@type="checkbox" and @name="%s" and @value="%s"]',
                $name,
                $value
            )
        );

        if (null === $nodeElement) {
            // Yikes! It aint where we want it
            throw new ExpectationException(
                sprintf(
                    'A checkbox with the value "%s" is not in the page.',
                    $value
                ),
                $this->getSession()->getDriver()
            );
        }

        // Make the magic happen
        $nodeElement->check();
    }
}
