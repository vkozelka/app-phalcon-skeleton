<?php

namespace App\Core;

use Phalcon\Mvc\Dispatcher as BaseDispatcher;

class Dispatcher extends BaseDispatcher
{

    protected string $section;

    public function getSection(): string
    {
        return $this->section;
    }

    public function setSection(string $section): void
    {
        $this->section = $section;
    }

}