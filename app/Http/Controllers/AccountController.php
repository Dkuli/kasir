<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {


        return view('account.index');
    }
}
