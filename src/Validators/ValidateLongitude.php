<?php

namespace ShabuShabu\Harness\Validators;

class ValidateLongitude
{
    public const REGEX = '^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))(\.(\d+))?)|180(\.0+)?)$';

    /**
     * @param mixed $a
     * @param mixed $value
     * @return bool
     */
    public function validate($a, $value): bool
    {
        if ($value > 180 || $value < -180) {
            return false;
        }

        return ! preg_match('/' . self::REGEX . '/', $value) ? false : true;
    }
}
