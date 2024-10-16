<?php

declare(strict_types=1);

namespace Flow\ETL\PHP\Type;

use Flow\ETL\Exception\InvalidArgumentException;
use Flow\ETL\PHP\Type\Logical\List\ListElement;
use Flow\ETL\PHP\Type\Logical\ListType;
use Flow\ETL\PHP\Type\Logical\Map\MapKey;
use Flow\ETL\PHP\Type\Logical\Map\MapValue;
use Flow\ETL\PHP\Type\Logical\MapType;
use Flow\ETL\PHP\Type\Logical\Structure\StructureElement;
use Flow\ETL\PHP\Type\Logical\StructureType;
use Flow\ETL\PHP\Type\Native\ArrayType;
use Flow\ETL\PHP\Type\Native\NullType;
use Flow\ETL\PHP\Type\Native\ObjectType;
use Flow\ETL\PHP\Type\Native\ScalarType;

final class TypeFactory
{
    public function getType(mixed $value) : Type
    {
        if (null === $value) {
            return new NullType();
        }

        if (\is_scalar($value)) {
            return ScalarType::fromString(\gettype($value));
        }

        if (\is_object($value)) {
            return ObjectType::of($value::class);
        }

        if (\is_array($value)) {
            if ([] === $value) {
                return ArrayType::empty();
            }

            $detector = new ArrayContentDetector(
                new Types(...\array_map([$this, 'getType'], \array_keys($value))),
                new Types(...\array_map([$this, 'getType'], \array_values($value)))
            );

            $firstKey = $detector->firstKeyType();
            $firstValue = $detector->firstValueType();

            if ($detector->isList() && $firstValue) {
                return new ListType(ListElement::fromType($firstValue));
            }

            if ($detector->isMap() && $firstKey && $firstValue) {
                return new MapType(MapKey::fromType($firstKey), MapValue::fromType($firstValue));
            }

            if ($detector->isStructure()) {
                $elements = [];

                foreach ($value as $key => $item) {
                    $elements[] = new StructureElement($key, $this->getType($item));
                }

                return new StructureType(...$elements);
            }

            return new ArrayType([] === \array_filter($value, fn ($value) : bool => null !== $value));
        }

        throw InvalidArgumentException::because('Unsupported type given: ' . \gettype($value));
    }
}
