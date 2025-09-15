<?php

namespace App\Services;

use App\Models\Rate;
use Illuminate\Support\Facades\Auth;



class RatesService
{
    public function getRates()
    {
        return Rate::select(
            'id',
            'user_id',
            'rateable_id',
            'rating',
            'comment',
            'rateable_type'
        )
            ->where('user_id', Auth::user()->id)
            ->get();
    }
    public function getRate($id)
    {
        return Rate::select(
            'id',
            'user_id',
            'rateable_id',
            'rateable_type',
            'rating',
            'comment',
        )
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
    }
    public function createRate($rate)
    {
        $data = Rate::create([
            'user_id' => Auth::user()->id,
            'rateable_id' => $rate['rateable_id'],
            'rateable_type' => $rate['rateable_type'],
            'rating' => $rate['rating'],
            'comment' => $rate['comment'],
        ]);
        return $data->fresh();
    }
    public function updateRate($id, $rate)
    {
        $data = Rate::select(
            'id',
            'user_id',
            'rateable_id',
            'rateable_type',
            'rating',
            'comment',
        )
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if (!$data)
            return null;
        $data->update($rate);
        return $data;
    }
    public function deleteRate($id)
    {
        $data = Rate::select('id', 'user_id')
            ->where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if (!$data)
            return false;
        $data->delete();
        return true;
    }
}
