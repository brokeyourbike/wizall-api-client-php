<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Responses;

use Spatie\DataTransferObject\Attributes\MapFrom;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class TransferCashResponse extends JsonResponse
{
    public ?string $code;
    public ?string $error;
    public ?string $id;
    public ?string $transactionid;

    #[MapFrom('Operation')]
    public ?string $operation;
}

