<?php

namespace Futureecom\OmnipayTranzila\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class Currency
 *
 * @method static Currency EURO()
 * @method static Currency GBP()
 * @method static Currency ILS()
 * @method static Currency USD()
 */
class Currency extends Enum
{
    protected const EUR = 987;
    protected const GBP = 826;
    protected const ILS = 1;
    protected const USD = 2;
}
