<?php

namespace App\Services;

use App\Models\User;

class UserService extends Service
{
    public function show($id)
    {
        $user = User::find($id);

        return view('backend.layout.user.show', compact('user'));
    }
}
