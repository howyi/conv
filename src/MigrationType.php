<?php

namespace Laminaria\Conv;

class MigrationType
{
    const CREATE            = 0;
    const ALTER             = 1;
    const DROP              = 2;
    const VIEW_CREATE       = 3;
    const VIEW_DROP         = 4;
    const CREATE_OR_REPLACE = 5;
    const VIEW_RENAME       = 6;
}
