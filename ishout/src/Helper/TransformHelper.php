<?php


namespace App\Helper;


class TransformHelper
{

    public function __invoke(string $string): string
    {
        if (empty($string)) {
            return '';
        }

        $string = strtoupper(trim($string));
        $substr = mb_substr($string, -1, 1);
        if ($substr === '!') {
            return $string;
        }

        if (!ctype_alnum($substr)) {
            $string = mb_substr($string, 0, -1);
        }

        return $string.'!';
    }
}