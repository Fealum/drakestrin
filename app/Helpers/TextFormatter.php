<?php

namespace App\Helpers;

use Genert\BBCode\BBCode;
use Parsedown;

class TextFormatter
{

    public static function format($text)
    {
        $text = self::escapeHtml($text);
        $text = self::convertBBCode($text);
        $text = self::convertMarkdown($text);
        //$text = self::convertLineBreaks($text);
        $text = self::replaceSmileys($text);
        $text = self::enhanceTypography($text);
        return $text;
    }

    /**
     * Escape HTML characters in a string.
     *
     * @param  string  $text
     * @return string
     */
    public static function escapeHtml($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    public static function convertBBCode($text)
    {
        $bbCode = new BBCode();

        $bbCode->addParser(
            'font-size',
            '/\[size\=(.*?)\](.*?)\[\/size\]/s',
            '<font size="$1">$2</font>',
            '$1'
        );

        return $bbCode->convertToHtml($text);
    }

    public static function convertMarkdown($text)
    {
        $parsedown = new Parsedown();

        return $parsedown->setBreaksEnabled(true)->text($text);
    }

    public static function convertLineBreaks($text)
    {
        $text = preg_replace("/\015\012|\015|\012/", "\n", $text);
        $text = preg_replace("/(\r\n|\r|\n){2,}/", '</p><p>', $text);
        $text = preg_replace("/(\r\n|\r|\n)/", '<br>', $text);

        if (!empty($text)) {
            $text = "<p>{$text}</p>";
        }

        return $text;
    }

    public static function replaceSmileys($text)
    {
        return $text;
    }

    public static function enhanceTypography($text)
    {
        $text = preg_replace("/\.\.\./", '&hellip;', $text);
        $text = preg_replace("/\s-\s/", '&nbsp;&ndash;&nbsp;', $text);
        $text = preg_replace("/\&quot;(.*?)\&quot;/", '&raquo;$1&laquo;', $text);

        return $text;
    }
}
