<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Behat\Helper\Regex;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ReformatRegex
{
    /**
     * @param string $regex
     * @return string
     */
    public static function spaceTabsEqual($regex)
    {
        return str_replace(
            ' ',
            '[\ ]*|[\t]*',
            $regex
        );
    }
}
