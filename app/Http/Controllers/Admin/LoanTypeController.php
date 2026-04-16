<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanType;
use Illuminate\Http\Request;

class LoanTypeController extends Controller
{

    public function index()
    {
        $types = LoanType::all();
        return view('loan_types.index', compact('types'));
    }

    public function create()
    {
        return view('loan_types.create');
    }

    public function store(Request $request)
    {
        LoanType::create($request->all());
        return redirect()->route('loan-types.index');
    }

}
