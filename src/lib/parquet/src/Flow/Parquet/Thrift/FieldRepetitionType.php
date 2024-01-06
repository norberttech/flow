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

/**
 * Representation of Schemas.
 */
final class FieldRepetitionType
{
    /**
     * The field is optional (can be null) and each record has 0 or 1 values.
     */
    public const OPTIONAL = 1;

    /**
     * The field is repeated and can contain 0 or more values.
     */
    public const REPEATED = 2;

    /**
     * This field is required (can not be null) and each record has exactly 1 value.
     */
    public const REQUIRED = 0;

    public static $__names = [
        0 => 'REQUIRED',
        1 => 'OPTIONAL',
        2 => 'REPEATED',
    ];
}
