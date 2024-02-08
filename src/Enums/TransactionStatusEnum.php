<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Enums;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
enum TransactionStatusEnum: string
{
    // when the token is already valid
    case INITIATED = 'Initiated';

    // when the token is paid
    case USED = 'Used';

    // when the transaction was cancelled
    case REVERSAL = 'Reversal';
}
