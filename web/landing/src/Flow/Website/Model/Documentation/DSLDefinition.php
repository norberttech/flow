<?php

declare(strict_types=1);

namespace Flow\Website\Model\Documentation;

final class DSLDefinition
{
    /**
     * @param array{
     *      name: string,
     *      namespace: string,
     *      parameters: array<mixed>,
     *      return_type: array<mixed>,
     *      attributes: array<mixed>,
     *      doc_comment: string|null,
     *  } $data
     */
    public function __construct(private readonly array $data)
    {
    }

    public function data() : array
    {
        return $this->data;
    }

    public function docComment() : string
    {
        return \base64_decode($this->data['doc_comment'], true);
    }

    public function hasDocComment() : bool
    {
        return $this->data['doc_comment'] !== null;
    }

    public function module() : ?string
    {
        foreach ($this->data['attributes'] as $attribute) {
            if ($attribute['name'] === 'DSL') {
                foreach ($attribute['arguments'] as $name => $argument) {
                    if ($name === 'module') {
                        return $argument;
                    }
                }
            }
        }

        return null;
    }

    public function name() : string
    {
        return $this->data['name'];
    }

    public function toString() : string
    {
        if ($this->hasDocComment()) {
            $output = $this->docComment() . PHP_EOL;
        } else {
            $output = '';
        }

        $output .= $this->data['name'];

        $output .= '(';

        $parameters = [];

        foreach ($this->data['parameters'] as $parameter) {
            $parameters[] = $this->parameterToString($parameter);
        }

        $output .= \implode(', ', $parameters);

        $output .= ') : ';

        $output .= $this->typeToString($this->data['return_type']);

        return $output;
    }

    public function type() : ?string
    {
        foreach ($this->data['attributes'] as $attribute) {
            if ($attribute['name'] === 'DSL') {
                foreach ($attribute['arguments'] as $name => $argument) {
                    if ($name === 'type') {
                        return $argument;
                    }
                }
            }
        }

        return null;
    }

    private function parameterToString(array $parameter) : string
    {
        $output = $this->typeToString($parameter['type']);
        $output .= ' $' . $parameter['name'];

        return $output;
    }

    private function typeToString(array $type) : string
    {
        $output = '';

        foreach ($type as $item) {
            if ($item['is_nullable'] && $item['name'] !== 'null') {
                $output .= '?';
            }

            $output .= $item['name'] . '|';

            if ($item['is_variadic']) {
                $output .= '...';
            }
        }

        return \rtrim($output, '|');
    }
}
