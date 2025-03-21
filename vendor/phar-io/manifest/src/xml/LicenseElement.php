<?php declare(strict_types = 1);
/*
 * This file is part of PharIo\Manifest.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Manifest;

class LicenseElement extends ManifestElement
{
    public function getType(): string
    {
        return $this->getAttributeValue('type');
    }

    public function getUrl(): string
    {
        return $this->getAttributeValue('url');
    }
}
