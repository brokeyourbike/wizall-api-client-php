<?php

// Copyright (C) 2024 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Interfaces;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface TransactionInterface
{
    public function getReference(): string;
    public function getAmount(): float;
    public function getCountry(): string;
    public function getRecipientPhone(): string;
    public function getRecipientFirstName(): string;
    public function getRecipientLastName(): string;
    public function getSenderFirstName(): string;
    public function getSenderLastName(): string;
    public function getSenderKycNumber(): string;
    public function getSenderKycType(): string;
    public function getSenderPhone(): string;
    public function getSenderEmail(): string;
}
