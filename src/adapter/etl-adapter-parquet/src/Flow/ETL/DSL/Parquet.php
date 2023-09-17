<?php

declare(strict_types=1);

namespace Flow\ETL\DSL;

use Flow\ETL\Adapter\Parquet\Codename\ParquetLoader;
use Flow\ETL\Adapter\Parquet\ParquetExtractor;
use Flow\ETL\Exception\MissingDependencyException;
use Flow\ETL\Extractor;
use Flow\ETL\Filesystem\Path;
use Flow\ETL\Loader;
use Flow\ETL\Row\EntryFactory;
use Flow\ETL\Row\Factory\NativeEntryFactory;
use Flow\ETL\Row\Schema;
use Flow\Parquet\ByteOrder;
use Flow\Parquet\Options;

/**
 * @infection-ignore-all
 */
class Parquet
{
    /**
     * @param array<Path>|Path|string $uri
     * @param array<string> $fields
     * @param EntryFactory $entry_factory
     *
     * @return Extractor
     */
    final public static function from(
        string|Path|array $uri,
        array $fields = [],
        Options $options = new Options(),
        ByteOrder $byte_order = ByteOrder::LITTLE_ENDIAN,
        int $rows_in_batch = 1000,
        EntryFactory $entry_factory = new NativeEntryFactory()
    ) : Extractor {
        if (\is_array($uri)) {
            $extractors = [];

            foreach ($uri as $filePath) {
                $extractors[] = new ParquetExtractor(
                    $filePath,
                    $options,
                    $byte_order,
                    $fields,
                    $rows_in_batch,
                    $entry_factory
                );
            }

            return new Extractor\ChainExtractor(...$extractors);
        }

        return new ParquetExtractor(
            \is_string($uri) ? Path::realpath($uri) : $uri,
            $options,
            $byte_order,
            $fields,
            $rows_in_batch,
            $entry_factory
        );
    }

    /**
     * @param Path|string $path
     * @param int $rows_in_group
     * @param null|Schema $schema
     *
     * @throws MissingDependencyException
     *
     * @return Loader
     */
    final public static function to(
        string|Path $path,
        int $rows_in_group = 1000,
        Schema $schema = null
    ) : Loader {
        return new ParquetLoader(
            \is_string($path) ? Path::realpath($path) : $path,
            $rows_in_group,
            $schema
        );
    }
}
