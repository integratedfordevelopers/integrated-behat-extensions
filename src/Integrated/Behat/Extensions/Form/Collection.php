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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Session;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
trait Collection
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I fill the fields of "([^"]*)" with value "([^"]*)"$/
     *
     * @param string $name
     * @param string $value
     * @throws ExpectationException
     */
    public function fillAllFieldsWithName($name, $value)
    {
        /** @var NodeElement[] $nodeElements */
        $nodeElements = $this->getSession()->getPage()->findAll('xpath', sprintf(
            '//input[starts-with(@name, "%s[")]',
            $name
        ));

        if (0 === count($nodeElements)) {
            throw new ExpectationException(
                sprintf('No input with the name "%s" is not found.', $name),
                $this->getSession()->getDriver()
            );
        }

        foreach ($nodeElements as $element) {
            $element->setValue($value);
        }
    }
}
