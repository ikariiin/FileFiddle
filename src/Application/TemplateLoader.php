<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: lelouch
 * Date: 11/9/16
 * Time: 4:29 PM
 */

namespace FileFiddle\Application;
use FileFiddle\Application\Keys\Keys;

class TemplateLoader {
    public static function loadHeaders(string $title = ""): string {
        $headers = file_get_contents(Keys::COMMON_HEADER);
        $vars = ["title" => $title];
        return self::resolveTemplate($vars, $headers);
    }

    public static function loadFooters(): string {
        return file_get_contents(Keys::COMMON_FOOTER);
    }

    /**
     * @param array $vars
     * @param string $content
     * @return string
     * @throws \RuntimeException
     */
    public static function resolveTemplate(array $vars, string $content): string {
        foreach ($vars as $k => $var) {
            if(!is_string($k)) {
                throw new \RuntimeException("The keys for the variables must be strings.");
            }

            $content = str_replace(
                sprintf(
                "{|%s|}",
                $k),
                $var,
                $content);
        }

        return $content;
    }
}