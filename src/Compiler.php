<?php

namespace RolfHaug\ModelSettings;

class Compiler
{
    public static function compile($template, $data)
    {
        foreach($data as $key => $value)
        {
            $template = preg_replace("/{{\s?$key\s?}}/i", $value, $template);
        }

        return $template;
    }
}