<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Node;

use League\CommonMark\Node\Inline\AbstractInline;

final class AttributesInline extends AbstractInline
{
    /**
     * @var array<string, mixed> 
     */
    private array $attributes;

    private bool $block;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes, bool $block)
    {
        parent::__construct();

        $this->attributes = $attributes;
        $this->block      = $block;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function isBlock(): bool
    {
        return $this->block;
    }
}
