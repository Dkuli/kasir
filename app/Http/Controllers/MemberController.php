<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberTier;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('tier')->latest()->get();
        return view('members.index', compact('members'));
    }

    public function create()
    {
        $tiers = MemberTier::all();
        $code = Member::generateCode();
        return view('members.create', compact('tiers', 'code'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:members,code',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members,email',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'tier_id' => 'required|exists:member_tiers,id',
        ]);

        $validated['is_active'] = true;
        $validated['points'] = 0;

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member berhasil ditambahkan!');
    }

    public function show(Member $member)
    {
        $member->load(['tier', 'pointTransactions' => function($query) {
            $query->latest();
        }]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $tiers = MemberTier::all();
        return view('members.edit', compact('member', 'tiers'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:members,code,'.$member->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:members,email,'.$member->id,
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'tier_id' => 'required|exists:member_tiers,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member berhasil diupdate!');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')
            ->with('success', 'Member berhasil dihapus!');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $members = Member::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('code', 'LIKE', "%{$query}%")
                  ->orWhere('name', 'LIKE', "%{$query}%")
                  ->orWhere('phone', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->get();

        return response()->json($members);
    }

    public function adjustPoints(Request $request, Member $member)
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'description' => 'required|string',
        ]);

        $pointsToAdjust = $validated['points'];

        if ($pointsToAdjust > 0) {
            $member->addPoints($pointsToAdjust, null, $validated['description']);
            $message = "Berhasil menambah {$pointsToAdjust} poin untuk member {$member->name}";
        } else {
            $success = $member->redeemPoints(abs($pointsToAdjust), null, $validated['description']);

            if (!$success) {
                return redirect()->back()->with('error', 'Poin tidak cukup untuk dilakukan pengurangan');
            }

            $message = "Berhasil mengurangi " . abs($pointsToAdjust) . " poin dari member {$member->name}";
        }

        return redirect()->route('members.show', $member)
            ->with('success', $message);
    }
}
