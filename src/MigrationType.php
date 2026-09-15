<?php

namespace Howyi\Conv;

class MigrationType
{
    public const CREATE            = 0;
    public const ALTER             = 1;
    public const DROP              = 2;
    public const VIEW_CREATE       = 3;
    public const VIEW_DROP         = 4;
    public const CREATE_OR_REPLACE = 5;
    public const VIEW_RENAME       = 6;
}
