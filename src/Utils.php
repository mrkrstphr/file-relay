<?php

class Utils
{
    public static function cleanTitle(string $title): string {
        $title = preg_replace("/:\s/", ' ', $title);
        // replace forward or backward slashes with spaces
        $title = preg_replace("/[\/\\\]/", ' ', $title);

        return $title;
    }

    public static function cleanUuid($input) {
        return preg_replace("/[^A-Za-z0-9- ]/", '', $input);
    }
}
