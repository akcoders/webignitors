<?php

namespace App\Exceptions;

use Carbon\CarbonInterface;
use RuntimeException;

class DailyReportLimitExceeded extends RuntimeException
{
    public function __construct(public readonly CarbonInterface $availableAt)
    {
        parent::__construct(
            'You can create one website report every 24 hours. Your next report is available '.$availableAt->diffForHumans().'.'
        );
    }
}
