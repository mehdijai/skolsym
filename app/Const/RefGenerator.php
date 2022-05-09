<?php
namespace App\Const;

use App\Models\Payment;
use Illuminate\Support\Str;

class RefGenerator
{
    public static function generate()
    {
        return strtoupper(Str::random(1)) . now()->getTimestamp();
    }

    public static function exists($uuid)
    {
        return Payment::where('ref', $uuid)->count() > 0;
    }


}
