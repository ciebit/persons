<?php
namespace Ciebit\Persons\Characteristics;

use MyCLabs\Enum\Enum;

class MaritalStatus extends Enum
{
    const UNDEFINED = 0;
    const SINGLE = 1;
    const MARRIED = 2;
    const DIVORCED = 3;
    const WIDOWED = 4;
}
