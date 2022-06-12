<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Libraries;

use SoapClient;

/**
 * @method mixed getCitiesCashPay(array $request)
 * @method mixed getTerminalsSelfDelivery2(array $request)
 * @method mixed getStatesByDPDOrder(array $request)
 * @method mixed getServiceCost2(array $request)
 */
class SoapService extends SoapClient
{
}
