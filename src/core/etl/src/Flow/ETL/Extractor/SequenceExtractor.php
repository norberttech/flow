<?php

declare(strict_types=1);

namespace Flow\ETL\Extractor;

use Flow\ETL\Extractor;
use Flow\ETL\FlowContext;
use Flow\ETL\Row;
use Flow\ETL\Rows;

final class SequenceExtractor implements Extractor
{
    public function __construct(
        private readonly SequenceGenerator\SequenceGenerator $generator,
        private readonly string $entryName = 'entry',
        private readonly Row\EntryFactory $entryFactory = new Row\Factory\NativeEntryFactory()
    ) {
    }

    public function extract(FlowContext $context) : \Generator
    {
        /** @var mixed $item */
        foreach ($this->generator->generate() as $item) {
            yield new Rows(Row::create($this->entryFactory->create($this->entryName, $item)));
        }
    }
}
