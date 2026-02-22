<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Month;

class MonthController extends Controller
{
    public function index()
    {
        $months=Month::all();
        return response()->json($months);
    }
}
