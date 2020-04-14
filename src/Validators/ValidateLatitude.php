<?php

namespace ShabuShabu\Harness\Validators;

class ValidateLatitude
{
    public const REGEX = '^[-]?(([0-8]?[0-9])(\.(\d+))?)|(90(\.0+)?)$';

    /**
     * @param mixed $a
     * @param mixed $value
     * @return bool
     */
    public function validate($a, $value): bool
    {
        if ($value > 90 || $value < -90) {
            return false;
        }

        return ! preg_match('/' . self::REGEX . '/', $value) ? false : true;
    }
}
