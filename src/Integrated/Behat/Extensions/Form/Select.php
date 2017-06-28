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
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
trait Select
{
    /**
     * @param null $name
     * @return Session
     */
    public abstract function getSession($name = null);

    /**
     * @Given /^I should see (asc|desc) sorted options in select "([^"]+)"$/
     *
     * @param string $sort
     * @param string $id
     * @throws ExpectationException
     */
    public function iShouldSeeSortedOptions($sort, $id)
    {
        /** @var NodeElement[] $nodeElements */
        $nodeElements = $this->getSession()->getPage()->findAll('xpath', sprintf('//select[@id="%s"]/option', $id));

        if (0 === count($nodeElements)) {
            throw new ExpectationException(
                sprintf('The select with id "%s" is not found or has no options.', $id),
                $this->getSession()->getDriver()
            );
        }

        $options = array_filter(array_map(
            function (NodeElement $element) {
                if ('disabled' == $element->getAttribute('disabled')) {
                    return null;
                }

                return $element->getText();
            },
            $nodeElements
        ));

        $sortedOptions = $options;
        $sort == 'asc' ? asort($sortedOptions) : arsort($sortedOptions);

        if ($options !== $sortedOptions) {
            throw new ExpectationException(
                sprintf('The options should be sorted "%s".', $sort),
                $this->getSession()->getDriver()
            );
        }
    }
}
