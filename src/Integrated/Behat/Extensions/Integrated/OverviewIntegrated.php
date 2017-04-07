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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait OverviewIntegrated
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @When /^I click the first icon "([^"]*)"$/
     *
     * @param string $class
     * @throws ExpectationException
     */
    public function clickFirstIcon($class)
    {
        $this->clickNode(sprintf('//span[@class="glyphicon %s"]/parent::a', $class));
    }

    /**
     * @When /^I click the last icon "([^"]*)"$/
     *
     * @param string $class
     * @throws ExpectationException
     */
    public function clickLastIcon($class)
    {
        $this->clickNode(sprintf('//span[@class="glyphicon %s"][last()]/parent::a', $class));
    }

    /**
     * @param string $xpath
     * @throws ExpectationException
     */
    protected function clickNode($xpath)
    {
        $nodeElement = $this->getSession()->getPage()->find('xpath', $xpath);

        if (null === $nodeElement) {
            // Yikes! No icon
            throw new ExpectationException(
                sprintf('The icon with class %s can not be found.', $class),
                $this->getSession()->getDriver()
            );
        }

        $nodeElement->click();
    }
}
