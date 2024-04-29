<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;


class UserReadController extends Controller
{


    public function index()
    {
        return UserProfile::all();   //
    }

    public function show(UserProfile $user)
    {
        return $user;
    }
}
