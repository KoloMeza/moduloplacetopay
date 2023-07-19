<?php

/**
 * Evertec PlacetoPay Payments Module (https://www.placetopay.com/)
 *
 * Copyright (c) 2019 Evertec PlacetoPay S.A.S. (https://www.placetopay.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Simon Godoy <simon.godoy@evertecinc.com>
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(ComponentRegistrar::MODULE, 'PlacetoPay_Payments', __DIR__);
