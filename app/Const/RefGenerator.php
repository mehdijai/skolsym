<?php
namespace App\Const;

use App\Models\Payment;

class RefGenerator
{
    public static function generate()
    {
        $timestamp = now()->getTimestamp();
        $val = dechex($timestamp + random_int(0, 9));
        $val = hexdec($val);
        $val = dechex($val + random_int(0, 9));
        $ref = strtoupper($val);

        if (self::exists($ref)) {
            $ref = self::generate();
        }

        return $ref;
    }

    public static function exists($uuid)
    {
        return Payment::where('ref', $uuid)->count() > 0;
    }

}
