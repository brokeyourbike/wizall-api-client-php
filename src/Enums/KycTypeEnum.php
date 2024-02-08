<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Enums;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
enum KycTypeEnum: string
{
    case ID_CARD = '1';
    case PASSPORT = '2';
}
