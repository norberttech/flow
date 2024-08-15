<?php

declare(strict_types=1);

namespace Flow\ETL\Attribute;

enum Module : string
{
    case AZURE_FILESYSTEM = 'Azure Filesystem';
    case AZURE_SDK = 'Azure SDK';
    case CHARTJS = 'ChartJS';
    case CORE = 'Core';
    case CSV = 'CSV';
    case DOCTRINE = 'Doctrine';
    case ELASTICSEARCH = 'Elastic Search';
    case FILESYSTEM = 'Filesystem';
    case GOOGLE_SHEET = 'Google Sheet';
    case JSON = 'JSON';
    case MEILI_SEARCH = 'MeiliSearch';
    case PARQUET = 'Parquet';
    case TEXT = 'Text';
    case XML = 'XML';
}
