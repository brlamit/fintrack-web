<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Show transaction details (web view)
     */
    public function show(Transaction $transaction)
    {
        $user = auth()->user();

        // If transaction belongs to a group, check that user is a member of that group
        if ($transaction->group_id) {
            $isMember = $transaction->group->members()->where('user_id', $user->id)->exists();
            if (! $isMember) {
                abort(403, 'Unauthorized');
            }
        } else {
            // Non-group transactions can only be viewed by owner
            if ($transaction->user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }
        }

        $transaction->load(['user', 'category', 'receipt', 'group']);

        return view('user.transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the edit form for the specified transaction
     */
    public function edit(Transaction $transaction)
    {
        $user = auth()->user();

        // Check ownership - only owner can edit
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $categories = $user->categories()
            ->orWhereNull('user_id')
            ->get();

        return view('user.transactions.edit', [
            'transaction' => $transaction->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        $user = auth()->user();

        // Check ownership
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:income,expense',
        ]);

        // Verify category belongs to user or is system category
        $category = \App\Models\Category::where('id', $validated['category_id'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereNull('user_id');
            })
            ->first();

        if (!$category) {
            return back()->withErrors(['category_id' => 'Invalid category']);
        }

        $transaction->update($validated);

        return redirect()->route('user.transactions')
            ->with('success', 'Transaction updated successfully!');
    }

    /**
     * Delete the specified transaction
     */
    public function destroy(Transaction $transaction)
    {
        $user = auth()->user();

        // Check ownership
        if ($transaction->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $transaction->delete();

        return redirect()->route('user.transactions')
            ->with('success', 'Transaction deleted successfully!');
    }
}


