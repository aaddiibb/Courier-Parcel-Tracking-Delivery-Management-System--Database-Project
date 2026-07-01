<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CustomerContext
{
    /**
     * The customers row linked to the currently authenticated user, or null
     * if their account has not been linked to a customer record yet.
     */
    public static function current(): ?object
    {
        $userId = auth()->id();

        if (! $userId) {
            return null;
        }

        $rows = DB::select(
            'SELECT customer_id, full_name, phone, email, address FROM customers WHERE user_id = :user_id',
            ['user_id' => $userId]
        );

        return $rows[0] ?? null;
    }

    public static function id(): ?int
    {
        return static::current()?->customer_id;
    }
}
