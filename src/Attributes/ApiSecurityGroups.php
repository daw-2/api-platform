<?php

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApiSecurityGroups
{
    public function __construct(public $groups)
    {
    }
}
