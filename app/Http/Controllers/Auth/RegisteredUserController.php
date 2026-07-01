<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:'.User::class],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        // Link to Oracle customers table — create record if email not already there
        $existing = DB::select(
            'SELECT customer_id FROM customers WHERE email = :email',
            ['email' => $request->email]
        );
        if (empty($existing)) {
            try {
                $customerId = DB::select("SELECT seq_customer_id.NEXTVAL AS id FROM DUAL")[0]->id;
                DB::insert(
                    "INSERT INTO customers (customer_id, full_name, phone, email, address, created_at)
                     VALUES (:id, :name, :phone, :email, NULL, SYSDATE)",
                    [
                        'id'    => $customerId,
                        'name'  => $request->name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                    ]
                );
            } catch (\Exception $e) {
                // Oracle record couldn't be created (e.g. duplicate phone); user account still works
            }
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('customer.dashboard');
    }
}
