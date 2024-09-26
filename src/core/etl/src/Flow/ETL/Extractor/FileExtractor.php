<?php

declare(strict_types=1);

namespace Flow\ETL\Extractor;

use Flow\Filesystem\Path;
use Flow\Filesystem\Path\Filter;

interface FileExtractor
{
    public function withPathFilter(Filter $filter) : self;

    /**
     * @deprecated Use withPathFilter instead
     */
    public function addFilter(Filter $filter) : self;

    public function filter() : Filter;

    public function source() : Path;
}
