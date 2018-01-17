<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017, 2018  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Eveapi\Jobs\Corporation;

use Seat\Eveapi\Jobs\EsiBase;
use Seat\Eveapi\Models\Corporation\CorporationMemberTracking;

class MemberTracking extends EsiBase {

    protected $method = 'get';

    protected $endpoint = '/corporations/{corporation_id}/membertracking/';

    protected $version = 'v1';

    public function handle()
    {

        $members = $this->retrieve([
            'corporation_id' => $this->getCorporationId(),
        ]);

        collect($members)->each(function($member){

            CorporationMemberTracking::firstOrNew([
                'corporation_id' => $this->getCorporationId(),
                'character_id'   => $member->character_id,
            ])->fill([
                'start_date'     => property_exists($member, 'start_date') ? carbon($member->start_date) : null,
                'base_id'        => $member->base_id ?? null,
                'logon_date'     => property_exists($member, 'logon_date') ? carbon($member->logon_date) : null,
                'logoff_date'    => property_exists($member, 'logoff_date') ? carbon($member->logoff_date) : null,
                'location_id'    => $member->location_id ?? null,
                'ship_type_id'   => $member->ship_type_id ?? null,
            ])->save();

        });

    }

}