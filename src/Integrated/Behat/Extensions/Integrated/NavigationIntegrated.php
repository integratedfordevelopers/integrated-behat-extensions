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
trait NavigationIntegrated
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I am going to create a "([^"]*)"$/
     *
     * @param string $name
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function createNewContent($name)
    {
        $this->getSession()->visit('/admin/content');
        $contentTypes = $this->getSession()->getPage()->findAll('xpath', '//div[@class="megamenu-content"]//li');

        if (is_array($contentTypes) && count($contentTypes)) {
            // Put the content type here
            $_element = null;

            // Iterate the content types in the list
            foreach ($contentTypes as $element) {
                /** @var \Behat\Mink\Element\NodeElement $element */
                if (false !== strpos($element->getText(), $name)) {
                    $_element = $element;
                }
            }

            if (null === $_element) {
                throw new ExpectationException(
                    sprintf('Content type %s not found under "Create new". do you have the correct rights?', $name),
                    $this->getSession()->getDriver()
                );
            }

            $_element->clickLink($name);
        } else {
            // No navigation found
            throw new ExpectationException(
                'No content items found under "Create new", are you sure you are loggedin?',
                $this->getSession()->getDriver()
            );
        }
    }
}
