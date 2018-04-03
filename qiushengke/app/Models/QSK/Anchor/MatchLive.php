<?php

namespace App\Models\QSK\Anchor;

use App\Models\QSK\Match\BasketMatch;
use App\Models\QSK\Match\Match;
use Illuminate\Database\Eloquent\Model;

class MatchLive extends Model
{
    protected $connection = 'qsk';

    const kSportFootball = 1, kSportBasketball = 2;

    public function football() {
        return $this->hasOne(Match::class, 'id', 'match_id');
    }


    public function basketball() {
        return $this->hasOne(BasketMatch::class, 'id', 'match_id');
    }

    public function getMatch() {
        if ($this->sport == self::kSportFootball) {
            return $this->football;
        } else if ($this->sport == self::kSportBasketball) {
            return $this->basketball;
        }
    }
}
