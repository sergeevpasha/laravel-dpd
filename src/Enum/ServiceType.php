<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Enum;

use BenSampo\Enum\Enum;

final class ServiceType extends Enum
{
    public const BZP = '18:00';
    public const ECN = 'ECONOMY';
    public const ECU = 'ECONOMY CU';
    public const CUR = 'CLASSIC';
    public const NDY = 'EXPRESS';
    public const CSM = 'Online Express';
    public const PCL = 'OPTIMUM';
    public const PUP = 'SHOP';
    public const DPI = 'CLASSIC international IMPORT';
    public const DPE = 'CLASSIC international EXPORT';
    public const MAX = 'MAX domestic';
    public const MXO = 'Online Max';
}
