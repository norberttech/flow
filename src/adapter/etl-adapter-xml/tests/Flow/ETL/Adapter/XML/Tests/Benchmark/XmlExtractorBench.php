<?php declare(strict_types=1);

namespace Flow\ETL\Adapter\XML\Tests\Benchmark;

use Flow\ETL\Config;
use Flow\ETL\FlowContext;
use PhpBench\Attributes\Groups;
use function Flow\ETL\Adapter\XML\from_xml;

#[Groups(['extractor'])]
final class XmlExtractorBench
{
    private readonly FlowContext $context;

    public function __construct()
    {
        $this->context = new FlowContext(Config::default());
    }

    public function bench_extract_10k() : void
    {
        foreach (from_xml(__DIR__ . '/../Fixtures/flow_orders.xml', xml_node_path: 'root/row')->extract($this->context) as $rows) {
        }
    }
}
