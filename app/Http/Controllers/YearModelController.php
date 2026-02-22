<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YearModel;

class YearModelController extends Controller
{
    public function index()
    {
        $years = YearModel::all();
        return response()->json($years);
    }
}
