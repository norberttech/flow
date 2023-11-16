<?php

declare(strict_types=1);

namespace Flow\ETL\DSL;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\Function\All;
use Flow\ETL\Function\Any;
use Flow\ETL\Function\ArrayExists;
use Flow\ETL\Function\ArrayExpand\ArrayExpand;
use Flow\ETL\Function\ArrayGet;
use Flow\ETL\Function\ArrayGetCollection;
use Flow\ETL\Function\ArrayKeyRename;
use Flow\ETL\Function\ArrayKeysStyleConvert;
use Flow\ETL\Function\ArrayMerge;
use Flow\ETL\Function\ArrayMergeCollection;
use Flow\ETL\Function\ArrayReverse;
use Flow\ETL\Function\ArraySort;
use Flow\ETL\Function\ArraySort\Sort;
use Flow\ETL\Function\ArrayUnpack;
use Flow\ETL\Function\Average;
use Flow\ETL\Function\CallMethod;
use Flow\ETL\Function\Cast;
use Flow\ETL\Function\Collect;
use Flow\ETL\Function\CollectUnique;
use Flow\ETL\Function\Combine;
use Flow\ETL\Function\Concat;
use Flow\ETL\Function\Count;
use Flow\ETL\Function\DateTimeFormat;
use Flow\ETL\Function\DensRank;
use Flow\ETL\Function\Exists;
use Flow\ETL\Function\First;
use Flow\ETL\Function\Hash;
use Flow\ETL\Function\Last;
use Flow\ETL\Function\Literal;
use Flow\ETL\Function\Max;
use Flow\ETL\Function\Min;
use Flow\ETL\Function\Not;
use Flow\ETL\Function\Now;
use Flow\ETL\Function\NumberFormat;
use Flow\ETL\Function\Optional;
use Flow\ETL\Function\PregMatch;
use Flow\ETL\Function\PregMatchAll;
use Flow\ETL\Function\PregReplace;
use Flow\ETL\Function\Rank;
use Flow\ETL\Function\Round;
use Flow\ETL\Function\RowNumber;
use Flow\ETL\Function\Sanitize;
use Flow\ETL\Function\ScalarFunction;
use Flow\ETL\Function\Size;
use Flow\ETL\Function\Split;
use Flow\ETL\Function\Sprintf;
use Flow\ETL\Function\StyleConverter\StringStyles;
use Flow\ETL\Function\Sum;
use Flow\ETL\Function\ToDate;
use Flow\ETL\Function\ToDateTime;
use Flow\ETL\Function\ToLower;
use Flow\ETL\Function\ToMoney;
use Flow\ETL\Function\ToTimeZone;
use Flow\ETL\Function\ToUpper;
use Flow\ETL\Function\Ulid;
use Flow\ETL\Function\Uuid;
use Flow\ETL\Function\When;
use Flow\ETL\Row;
use Flow\ETL\Row\EntryFactory;
use Flow\ETL\Row\EntryReference;
use Flow\ETL\Row\Factory\NativeEntryFactory;
use Flow\ETL\Row\Reference;
use Flow\ETL\Row\StructureReference;
use Flow\ETL\Rows;
use Flow\ETL\Window;

function col(string $entry, string ...$entries) : Reference
{
    if ([] !== $entries) {
        return new StructureReference($entry, ...$entries);
    }

    return new EntryReference($entry);
}

function entry(string $entry) : EntryReference
{
    return new EntryReference($entry);
}

/**
 * Alias for entry function.
 */
function ref(string $entry) : EntryReference
{
    return entry($entry);
}

function optional(ScalarFunction $expression) : ScalarFunction
{
    return new Optional($expression);
}

function struct(string ...$entries) : StructureReference
{
    if (!\count($entries)) {
        throw new InvalidArgumentException('struct (StructureReference) require at least one entry');
    }

    $entry = \array_shift($entries);

    return new StructureReference($entry, ...$entries);
}

function lit(mixed $value) : ScalarFunction
{
    return new Literal($value);
}

function exists(ScalarFunction $ref) : ScalarFunction
{
    return new Exists($ref);
}

function when(ScalarFunction $ref, ScalarFunction $then, ?ScalarFunction $else = null) : ScalarFunction
{
    return new When($ref, $then, $else);
}

function array_get(ScalarFunction $ref, string $path) : ScalarFunction
{
    return new ArrayGet($ref, $path);
}

function array_get_collection(ScalarFunction $ref, string ...$keys) : ScalarFunction
{
    return new ArrayGetCollection($ref, $keys);
}

function array_get_collection_first(ScalarFunction $ref, string ...$keys) : ScalarFunction
{
    return ArrayGetCollection::fromFirst($ref, $keys);
}

function array_exists(ScalarFunction $ref, string $path) : ScalarFunction
{
    return new ArrayExists($ref, $path);
}

function array_merge(ScalarFunction $left, ScalarFunction $right) : ScalarFunction
{
    return new ArrayMerge($left, $right);
}

function array_merge_collection(ScalarFunction $ref) : ScalarFunction
{
    return new ArrayMergeCollection($ref);
}

function array_key_rename(ScalarFunction $ref, string $path, string $newName) : ScalarFunction
{
    return new ArrayKeyRename($ref, $path, $newName);
}

function array_keys_style_convert(ScalarFunction $ref, StringStyles|string $style = StringStyles::SNAKE) : ScalarFunction
{
    return new ArrayKeysStyleConvert($ref, $style instanceof StringStyles ? $style : StringStyles::fromString($style));
}

function array_sort(ScalarFunction $expression, ?string $function = null, ?int $flags = null, bool $recursive = true) : ScalarFunction
{
    return new ArraySort($expression, $function ? Sort::fromString($function) : Sort::sort, $flags, $recursive);
}

function array_reverse(ScalarFunction $expression, bool $preserveKeys = false) : ScalarFunction
{
    return new ArrayReverse($expression, $preserveKeys);
}

function now(\DateTimeZone $time_zone = new \DateTimeZone('UTC')) : ScalarFunction
{
    return new Now($time_zone);
}

function to_date_time(ScalarFunction $ref, string $format = 'Y-m-d H:i:s', \DateTimeZone $timeZone = new \DateTimeZone('UTC')) : ScalarFunction
{
    return new ToDateTime($ref, $format, $timeZone);
}

function to_date(ScalarFunction $ref, string $format = 'Y-m-d', \DateTimeZone $timeZone = new \DateTimeZone('UTC')) : ScalarFunction
{
    return new ToDate($ref, $format, $timeZone);
}

function date_time_format(ScalarFunction $ref, string $format) : ScalarFunction
{
    return new DateTimeFormat($ref, $format);
}

/**
 * @param non-empty-string $separator
 */
function split(ScalarFunction $ref, string $separator, int $limit = PHP_INT_MAX) : ScalarFunction
{
    return new Split($ref, $separator, $limit);
}

function combine(ScalarFunction $keys, ScalarFunction $values) : ScalarFunction
{
    return new Combine($keys, $values);
}

function concat(ScalarFunction ...$expressions) : ScalarFunction
{
    return new Concat(...$expressions);
}

function hash(ScalarFunction $expression, string $algorithm = 'xxh128', bool $binary = false, array $options = []) : ScalarFunction
{
    return new Hash($expression, $algorithm, $binary, $options);
}

function cast(ScalarFunction $expression, string $type) : ScalarFunction
{
    return new Cast($expression, $type);
}

function count(ScalarFunction $expression) : Count
{
    return new Count($expression);
}

/**
 * Unpacks each element of an array into a new entry, using the array key as the entry name.
 *
 * Before:
 * +--+-------------------+
 * |id|              array|
 * +--+-------------------+
 * | 1|{"a":1,"b":2,"c":3}|
 * | 2|{"d":4,"e":5,"f":6}|
 * +--+-------------------+
 *
 * After:
 * +--+-----+-----+-----+-----+-----+
 * |id|arr.b|arr.c|arr.d|arr.e|arr.f|
 * +--+-----+-----+-----+-----+-----+
 * | 1|    2|    3|     |     |     |
 * | 2|     |     |    4|    5|    6|
 * +--+-----+-----+-----+-----+-----+
 */
function array_unpack(ScalarFunction $expression, array $skip_keys = [], ?string $entry_prefix = null) : ScalarFunction
{
    return new ArrayUnpack($expression, $skip_keys, $entry_prefix);
}

/**
 * Expands each value into entry, if there are more than one value, multiple rows will be created.
 * Array keys are ignored, only values are used to create new rows.
 *
 * Before:
 *   +--+-------------------+
 *   |id|              array|
 *   +--+-------------------+
 *   | 1|{"a":1,"b":2,"c":3}|
 *   +--+-------------------+
 *
 * After:
 *   +--+--------+
 *   |id|expanded|
 *   +--+--------+
 *   | 1|       1|
 *   | 1|       2|
 *   | 1|       3|
 *   +--+--------+
 */
function array_expand(ScalarFunction $expression, ArrayExpand $expand = ArrayExpand::VALUES) : ScalarFunction
{
    return new \Flow\ETL\Function\ArrayExpand($expression, $expand);
}

function size(ScalarFunction $expression) : ScalarFunction
{
    return new Size($expression);
}

function uuid_v4() : ScalarFunction
{
    return Uuid::uuid4();
}

function uuid_v7(?ScalarFunction $expression = null) : ScalarFunction
{
    return Uuid::uuid7($expression);
}

function ulid(?ScalarFunction $expression = null) : ScalarFunction
{
    return new Ulid($expression);
}

function lower(ScalarFunction $expression) : ScalarFunction
{
    return new ToLower($expression);
}

function upper(ScalarFunction $expression) : ScalarFunction
{
    return new ToUpper($expression);
}

function call_method(ScalarFunction $object, ScalarFunction $method, ScalarFunction ...$params) : ScalarFunction
{
    return new CallMethod($object, $method, ...$params);
}

function all(ScalarFunction ...$expressions) : ScalarFunction
{
    return new All(...$expressions);
}

function any(ScalarFunction ...$expressions) : ScalarFunction
{
    return new Any(...$expressions);
}

function not(ScalarFunction $expression) : ScalarFunction
{
    return new Not($expression);
}

function to_timezone(ScalarFunction $expression, ScalarFunction $timeZone) : ScalarFunction
{
    return new ToTimeZone($expression, $timeZone);
}

function to_money(ScalarFunction $amount, ScalarFunction $currency, ?\Money\MoneyParser $moneyParser = null) : ScalarFunction
{
    if (null !== $moneyParser) {
        return new ToMoney($amount, $currency, $moneyParser);
    }

    return new ToMoney($amount, $currency);
}

function regex_replace(ScalarFunction $pattern, ScalarFunction $replacement, ScalarFunction $subject) : ScalarFunction
{
    return new PregReplace($pattern, $replacement, $subject);
}

function regex_match_all(ScalarFunction $pattern, ScalarFunction $subject, ?ScalarFunction $flags = null) : ScalarFunction
{
    return new PregMatchAll($pattern, $subject, $flags);
}

function regex_match(ScalarFunction $pattern, ScalarFunction $subject) : ScalarFunction
{
    return new PregMatch($pattern, $subject);
}

function sprintf(ScalarFunction $format, ScalarFunction ...$args) : ScalarFunction
{
    return new Sprintf($format, ...$args);
}

function sanitize(ScalarFunction $expression, ?ScalarFunction $placeholder = null, ?ScalarFunction $skipCharacters = null) : ScalarFunction
{
    return new Sanitize($expression, $placeholder ?: new Literal('*'), $skipCharacters ?: new Literal(0));
}

/**
 * @param ScalarFunction $expression
 * @param null|ScalarFunction $precision
 * @param int<0, max> $mode
 *
 * @return ScalarFunction
 */
function round(ScalarFunction $expression, ?ScalarFunction $precision = null, int $mode = PHP_ROUND_HALF_UP) : ScalarFunction
{
    return new Round($expression, $precision ?? lit(2), $mode);
}

function number_format(ScalarFunction $expression, ?ScalarFunction $decimals = null, ?ScalarFunction $decimalSeparator = null, ?ScalarFunction $thousandsSeparator = null) : ScalarFunction
{
    if ($decimals === null) {
        $decimals = lit(0);
    }

    if ($decimalSeparator === null) {
        $decimalSeparator = lit('.');
    }

    if ($thousandsSeparator === null) {
        $thousandsSeparator = lit(',');
    }

    return new NumberFormat($expression, $decimals, $decimalSeparator, $thousandsSeparator);
}

/**
 * @psalm-suppress PossiblyInvalidIterator
 *
 * @param array<array<mixed>>|array<mixed|string> $data
 */
function array_to_rows(array $data, EntryFactory $entryFactory = new NativeEntryFactory()) : Rows
{
    $isRows = true;

    foreach ($data as $v) {
        if (!\is_array($v)) {
            $isRows = false;

            break;
        }
    }

    if (!$isRows) {
        $entries = [];

        foreach ($data as $key => $value) {
            $entries[] = $entryFactory->create(\is_int($key) ? 'e' . \str_pad((string) $key, 2, '0', STR_PAD_LEFT) : $key, $value);
        }

        return new Rows(Row::create(...$entries));
    }
    $rows = [];

    foreach ($data as $row) {
        $entries = [];

        foreach ($row as $column => $value) {
            $entries[] = $entryFactory->create(\is_int($column) ? 'e' . \str_pad((string) $column, 2, '0', STR_PAD_LEFT) : $column, $value);
        }
        $rows[] = Row::create(...$entries);
    }

    return new Rows(...$rows);
}

function rank() : Rank
{
    return new Rank();
}

function dens_rank() : DensRank
{
    return new DensRank();
}

function average(EntryReference $ref) : Average
{
    return new Average($ref);
}

function collect(Reference $ref) : Collect
{
    return new Collect($ref);
}

function collect_unique(Reference $ref) : CollectUnique
{
    return new CollectUnique($ref);
}

function window() : Window
{
    return new Window();
}

function sum(Reference $expression) : Sum
{
    return new Sum($expression);
}

function first(Reference $ref) : First
{
    return new First($ref);
}

function last(Reference $ref) : Last
{
    return new Last($ref);
}

function max(Reference $ref) : Max
{
    return new Max($ref);
}

function min(Reference $ref) : Min
{
    return new Min($ref);
}

function row_number() : RowNumber
{
    return new RowNumber();
}
