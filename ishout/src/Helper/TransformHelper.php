<?php


namespace App\Helper;


class TransformHelper
{

    public static function shout(string $string): string
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

    public static function slugify($string): string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', trim($slug));
        $slug = strtolower(trim($slug, '-'));
        $slug = preg_replace("/[\/_|+ -]+/", '-', $slug);
        return $slug;
    }
}