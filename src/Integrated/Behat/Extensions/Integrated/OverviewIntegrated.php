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

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait OverviewIntegrated
{
    /**
     * @return Session
     */
    public abstract function getSession();

    /**
     * @When /^I am click icon "([^"]*)"$/
     *
     * @param string $class
     * @throws ExpectationException
     */
    public function clickIcon($class)
    {
        /** @var \Behat\Mink\Element\NodeElement $link */
        $link = $this->getSession()->getPage()->find(
            'xpath',
            sprintf('//span[@class="glyphicon %s"]/parent::a', $class)
        );

        if (null === $link) {
            // Yikes! No icon
            throw new ExpectationException(
                sprintf('The icon with class %s can not be found.', $class),
                $this->getSession()->getDriver()
            );
        }

        $link->click();
    }
}
