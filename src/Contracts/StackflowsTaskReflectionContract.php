<?php

namespace Stackflows\Contracts;

interface StackflowsTaskReflectionContract
{
    public static function getStackflowsReferenceKeyName(): string;

    public static function getStackflowsActivityKeyName(): string;
}
