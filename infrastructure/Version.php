<?php

namespace Infrastructure;

class Version
{
    public static function getGitTag()
    {
        $versionFile = base_path('version.txt');
        return file_exists($versionFile) ? file_get_contents($versionFile) : exec('git describe --tags');
    }
}
