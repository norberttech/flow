<?php

declare(strict_types=1);

namespace Flow\Parquet\Thrift;

/**
 * Autogenerated by Thrift Compiler (0.19.0).
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *
 *  @generated
 */
final class PageType
{
    public const DATA_PAGE = 0;

    public const DATA_PAGE_V2 = 3;

    public const DICTIONARY_PAGE = 2;

    public const INDEX_PAGE = 1;

    public static $__names = [
        0 => 'DATA_PAGE',
        1 => 'INDEX_PAGE',
        2 => 'DICTIONARY_PAGE',
        3 => 'DATA_PAGE_V2',
    ];
}
