<?php

declare(strict_types=1);

namespace Flow\ETL\Tests\Integration\Row\Reference\Expression;

use function Flow\ETL\DSL\array_get;
use function Flow\ETL\DSL\concat;
use function Flow\ETL\DSL\lit;
use function Flow\ETL\DSL\ref;
use Flow\ETL\DSL\From;
use Flow\ETL\DSL\To;
use Flow\ETL\Flow;
use Flow\ETL\Memory\ArrayMemory;
use PHPUnit\Framework\TestCase;

final class ConcatTest extends TestCase
{
    public function test_concat_on_non_string_value() : void
    {
        (new Flow())
            ->read(
                From::array(
                    [
                        ['id' => 1],
                        ['id' => 2],
                    ]
                )
            )
            ->withEntry('concat', concat(ref('id'), lit(null)))
            ->write(To::memory($memory = new ArrayMemory()))
            ->run();

        $this->assertSame(
            [
                ['id' => 1, 'concat' => null],
                ['id' => 2, 'concat' => null],
            ],
            $memory->data
        );
    }

    public function test_concat_on_stringable_value() : void
    {
        (new Flow())
            ->read(
                From::array(
                    [
                        ['id' => 1, 'array' => ['field' => 'value']],
                        ['id' => 2],
                    ]
                )
            )
            ->withEntry('concat', concat(ref('id'), lit('-'), array_get(ref('array'), 'field')))
            ->drop('array')
            ->write(To::memory($memory = new ArrayMemory()))
            ->run();

        $this->assertSame(
            [
                ['id' => 1, 'concat' => '1-value'],
                ['id' => 2, 'concat' => null],
            ],
            $memory->data
        );
    }
}
