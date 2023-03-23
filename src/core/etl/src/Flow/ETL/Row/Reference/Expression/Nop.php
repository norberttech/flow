<?php

declare(strict_types=1);

namespace Flow\ETL\Row\Reference\Expression;

use Flow\ETL\Row;
use Flow\ETL\Row\Reference\Expression;

final class Nop implements Expression
{
    public function __construct()
    {
    }

    public function eval(Row $row) : mixed
    {
        return null;
    }
}
