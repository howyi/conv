<?php

namespace Conv;

class MigrationType
{
    const CREATE            = 0;
    const ALTER             = 1;
    const DROP              = 2;
    const CREATE_OR_REPLACE = 3;
    const VIEW_RENAME       = 4;
}
