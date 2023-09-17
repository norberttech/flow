<?php declare(strict_types=1);
namespace Flow\Parquet\Thrift;

/**
 * Autogenerated by Thrift Compiler (0.19.0).
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;

/**
 * Bloom filter header is stored at beginning of Bloom filter data of each column
 * and followed by its bitset.
 */
class BloomFilterHeader extends TBase
{
    public static $_TSPEC = [
        1 => [
            'var' => 'numBytes',
            'isRequired' => true,
            'type' => TType::I32,
        ],
        2 => [
            'var' => 'algorithm',
            'isRequired' => true,
            'type' => TType::STRUCT,
            'class' => '\Flow\Parquet\Thrift\BloomFilterAlgorithm',
        ],
        3 => [
            'var' => 'hash',
            'isRequired' => true,
            'type' => TType::STRUCT,
            'class' => '\Flow\Parquet\Thrift\BloomFilterHash',
        ],
        4 => [
            'var' => 'compression',
            'isRequired' => true,
            'type' => TType::STRUCT,
            'class' => '\Flow\Parquet\Thrift\BloomFilterCompression',
        ],
    ];

    public static $isValidate = false;

    /**
     * The algorithm for setting bits. *.
     *
     * @var \Flow\Parquet\Thrift\BloomFilterAlgorithm
     */
    public $algorithm;

    /**
     * The compression used in the Bloom filter *.
     *
     * @var \Flow\Parquet\Thrift\BloomFilterCompression
     */
    public $compression;

    /**
     * The hash function used for Bloom filter. *.
     *
     * @var \Flow\Parquet\Thrift\BloomFilterHash
     */
    public $hash;

    /**
     * The size of bitset in bytes *.
     *
     * @var int
     */
    public $numBytes;

    public function __construct($vals = null)
    {
        if (\is_array($vals)) {
            parent::__construct(self::$_TSPEC, $vals);
        }
    }

    public function getName()
    {
        return 'BloomFilterHeader';
    }

    public function read($input)
    {
        return $this->_read('BloomFilterHeader', self::$_TSPEC, $input);
    }

    public function write($output)
    {
        return $this->_write('BloomFilterHeader', self::$_TSPEC, $output);
    }
}
