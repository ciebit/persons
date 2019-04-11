<?php
namespace Ciebit\Persons\Characteristics;

use MyCLabs\Enum\Enum;

class EducationalLevel extends Enum
{
    const UNDEFINED = 0;
    const NONE = 1;
    const PRESCHOOL = 2;
    const ELEMENTARY_SCHOOL = 3;
    const MIDDLE_SCHOOL = 4;
    const HIGH_SCHOOL = 5;
    const HIGHER_EDUCATION = 6;
}
