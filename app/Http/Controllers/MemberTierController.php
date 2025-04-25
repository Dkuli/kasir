<?php

namespace App\Http\Controllers;

use App\Models\MemberTier;
use Illuminate\Http\Request;

class MemberTierController extends Controller
{
    public function index()
    {
        $tiers = MemberTier::orderBy('min_points')->get();
        return view('member-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('member-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_points' => 'required|integer|min:0',
            'points_multiplier' => 'required|numeric|min:0.01',
            'benefits' => 'nullable|string',
        ]);

        MemberTier::create($validated);

        return redirect()->route('member-tiers.index')
            ->with('success', 'Tier member berhasil dibuat!');
    }

    public function edit(MemberTier $memberTier)
    {
        return view('member-tiers.edit', compact('memberTier'));
    }

    public function update(Request $request, MemberTier $memberTier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_points' => 'required|integer|min:0',
            'points_multiplier' => 'required|numeric|min:0.01',
            'benefits' => 'nullable|string',
        ]);

        $memberTier->update($validated);

        return redirect()->route('member-tiers.index')
            ->with('success', 'Tier member berhasil diupdate!');
    }

    public function destroy(MemberTier $memberTier)
    {
        // Check if members are using this tier
        if ($memberTier->members()->count() > 0) {
            return redirect()->route('member-tiers.index')
                ->with('error', 'Tidak dapat menghapus tier yang sedang digunakan!');
        }

        $memberTier->delete();

        return redirect()->route('member-tiers.index')
            ->with('success', 'Tier member berhasil dihapus!');
    }
}
