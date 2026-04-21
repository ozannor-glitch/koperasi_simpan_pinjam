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
    $request->validate([
        'name' => 'required',
        'max_plafon' => 'required|numeric|min:1',
        'interest_rate_percent' => 'required|numeric|min:0',
        'max_tenor_months' => 'required|integer|min:1',
        'collateral_ratio' => 'nullable|numeric|min:1|max:100',
        'interest_method' => 'required|in:flat,efektif,anuitas',
    ]);

    LoanType::create([
        'name' => $request->name,
        'max_plafon' => $request->max_plafon,
        'interest_rate_percent' => $request->interest_rate_percent,
        'max_tenor_months' => $request->max_tenor_months,
        'collateral_ratio' => $request->collateral_ratio ?? 70,
        'interest_method' => $request->interest_method,
    ]);

    return redirect()->route('loan-types.index')
        ->with('success', 'Jenis pinjaman berhasil dibuat');
}

}
