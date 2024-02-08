<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Responses;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferCashStatusResponse extends JsonResponse
{
    public ?string $code;
    public ?string $error;

    #[MapFrom('TokenStatus')]
    public ?string $status;

    #[MapFrom('Operation')]
    public ?string $operation;
}

