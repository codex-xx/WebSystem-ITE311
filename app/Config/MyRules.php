<?php

namespace Config;

class MyRules
{
    public static function valid_time(string $str): bool
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $str);
    }
}
