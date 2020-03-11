<?php

namespace Utils;

trait Snake
{
    /**
     * Convert a string to snake case.
     *
     * @param string $input
     * @return string
     */
    public static function snake($input)
    {
        /** @see https://stackoverflow.com/a/19533226/7720656 */
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
    }
}
