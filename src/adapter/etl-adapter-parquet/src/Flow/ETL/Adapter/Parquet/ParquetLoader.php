<?php declare(strict_types=1);

namespace Flow\ETL\Adapter\Parquet;

use Flow\ETL\Filesystem\Path;
use Flow\ETL\Filesystem\Stream\Mode;
use Flow\ETL\FlowContext;
use Flow\ETL\Loader;
use Flow\ETL\Loader\Closure;
use Flow\ETL\Row\Schema;
use Flow\ETL\Rows;
use Flow\Parquet\Options;
use Flow\Parquet\ParquetFile\Compressions;
use Flow\Parquet\Writer;

/**
 * @implements Loader<array{
 *   path: Path
 * }>
 */
final class ParquetLoader implements Closure, Loader, Loader\FileLoader
{
    private readonly SchemaConverter $converter;

    private ?Schema $inferredSchema = null;

    /**
     * @var array<string, Writer>
     */
    private array $writers = [];

    public function __construct(
        private readonly Path $path,
        private readonly Options $options,
        private readonly Compressions $compressions = Compressions::SNAPPY,
        private readonly ?Schema $schema = null,
    ) {
        $this->converter = new SchemaConverter();

        if ($this->path->isPattern()) {
            throw new \InvalidArgumentException("ParquetLoader path can't be pattern, given: " . $this->path->path());
        }
    }

    public function __serialize() : array
    {
        return [
            'path' => $this->path,
        ];
    }

    public function __unserialize(array $data) : void
    {
        $this->path = $data['path'];
    }

    public function closure(FlowContext $context) : void
    {
        if (\count($this->writers)) {
            foreach ($this->writers as $writer) {
                $writer->close();
            }
        }

        $context->streams()->close($this->path);
        $this->writers = [];
    }

    public function destination() : Path
    {
        return $this->path;
    }

    public function load(Rows $rows, FlowContext $context) : void
    {
        if ($this->schema === null) {
            $this->inferSchema($rows);
        }

        $streams = $context->streams();

        if ($context->partitionEntries()->count()) {
            foreach ($rows->partitionBy(...$context->partitionEntries()->all()) as $partitions) {

                $stream = $streams->open($this->path, 'parquet', Mode::WRITE_BINARY, $context->appendSafe(), $partitions->partitions);

                if (!\array_key_exists($stream->path()->uri(), $this->writers)) {
                    $this->writers[$stream->path()->uri()] = new Writer(
                        compression: $this->compressions,
                        options: $this->options
                    );

                    $this->writers[$stream->path()->uri()]->openForStream($stream->resource(), $this->converter->toParquet($this->schema()));
                }

                foreach ($partitions->rows as $row) {
                    $this->writers[$stream->path()->uri()]->writeRow($row->toArray());
                }
            }
        } else {
            $stream = $streams->open($this->path, 'parquet', Mode::WRITE_BINARY, $context->appendSafe());

            if (!\array_key_exists($stream->path()->uri(), $this->writers)) {
                $this->writers[$stream->path()->uri()] = new Writer(
                    compression: $this->compressions,
                    options: $this->options
                );

                $this->writers[$stream->path()->uri()]->openForStream($stream->resource(), $this->converter->toParquet($this->schema()));
            }

            foreach ($rows as $row) {
                $this->writers[$stream->path()->uri()]->writeRow($row->toArray());
            }
        }
    }

    private function inferSchema(Rows $rows) : void
    {
        if ($this->inferredSchema === null) {
            $this->inferredSchema = $rows->schema();
        } else {
            $this->inferredSchema = $this->inferredSchema->merge($rows->schema());
        }
    }

    /**
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    private function schema() : Schema
    {
        /** @phpstan-ignore-next-line  */
        return $this->schema ?? $this->inferredSchema;
    }
}
