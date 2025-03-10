<?php

/*
 * Nytris Turbo - Turbocharged HTTP/FastCGI for PHP.
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/turbo/
 *
 * Released under the MIT license.
 * https://github.com/nytris/turbo/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Nytris\Turbo\Component\Cylinder;

use Nytris\Turbo\Turbo;

/**
 * Class CylinderProvider.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CylinderProvider implements CylinderProviderInterface
{
    /**
     * @inheritDoc
     */
    public function provideCylinder(): CylinderInterface
    {
        return Turbo::getCylinder();
    }
}
