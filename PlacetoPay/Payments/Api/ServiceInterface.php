<?php

namespace PlacetoPay\Payments\Api;

/**
 * Interface ServiceInterface.
 */
interface ServiceInterface
{
    /**
     * @return array
     */
    public function notify(): array;
}
