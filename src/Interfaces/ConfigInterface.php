<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
// Use of this source code is governed by a BSD-style
// license that can be found in the LICENSE file.

namespace BrokeYourBike\Wizall\Interfaces;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
interface ConfigInterface
{
    public function getUrl(): string;
    public function getClientId(): string;
    public function getClientSecret(): string;
    public function getUsername(): string;
    public function getPassword(): string;
    public function getCountry(): string;
    public function getAgentMsisdn(): string;
    public function getAgentPin(): string;
    public function getSenderPhone(): string;
    public function getSenderEmail(): string;
}
