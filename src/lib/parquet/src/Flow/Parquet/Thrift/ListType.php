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

class ListType extends TBase
{
    public static $_TSPEC = [
    ];

    public static $isValidate = false;

    public function __construct()
    {
    }

    public function getName()
    {
        return 'ListType';
    }

    public function read($input)
    {
        return $this->_read('ListType', self::$_TSPEC, $input);
    }

    public function write($output)
    {
        return $this->_write('ListType', self::$_TSPEC, $output);
    }
}
