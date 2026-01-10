<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Group;
use App\Http\Controllers\GroupController;
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
        $isAdmin = false;
        if ($transaction->group_id) {
            $member = $transaction->group->members()->where('user_id', $user->id)->first();
            if (!$member) {
                abort(403, 'Unauthorized');
            }
            $isAdmin = $member->role === 'admin';
        } else {
            // Non-group transactions can only be viewed by owner
            if ($transaction->user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }
        }

        $transaction->load(['user', 'category', 'receipt', 'group.members.user']);

        $categories = $user->categories()
            ->orWhereNull('user_id')
            ->get();

        $groupMembers = null;
        $relatedTransactions = collect();
        if ($transaction->group) {
            $groupMembers = $transaction->group->members;
            
            // Find related transactions in the same split group
            $relatedTransactions = Transaction::where('group_id', $transaction->group_id)
                ->where('description', $transaction->description)
                ->where('category_id', $transaction->category_id)
                ->where('type', $transaction->type)
                ->whereDate('created_at', $transaction->created_at->toDateString())
                ->whereBetween('created_at', [
                    $transaction->created_at->subSeconds(2),
                    $transaction->created_at->addSeconds(2)
                ])
                ->get();
        }

        return view('user.transactions.show', [
            'transaction' => $transaction,
            'categories' => $categories,
            'isAdmin' => $isAdmin,
            'groupMembers' => $groupMembers,
            'relatedTransactions' => $relatedTransactions,
        ]);
    }

    /**
     * Show the edit form for the specified transaction
     */
    public function edit(Transaction $transaction)
    {
        $user = auth()->user();

        // Check ownership/permissions
        if ($transaction->group_id) {
            $group = Group::findOrFail($transaction->group_id);
            $member = $group->members()->where('user_id', $user->id)->first();
            $isAdmin = $member && $member->role === 'admin';
            $isCreator = $transaction->user_id === $user->id;

            if (!$isCreator && !$isAdmin) {
                abort(403, 'Unauthorized. Only admins or the creator can edit this transaction.');
            }
        } elseif ($transaction->user_id !== $user->id) {
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
        // If it's a group transaction, use the group logic
        if ($transaction->group_id) {
            $group = Group::findOrFail($transaction->group_id);
            $response = app(GroupController::class)->updateTransaction($request, $group, $transaction);
            
            // If it's a JSON response (standard for that method), we might need to handle it for web
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $data = $response->getData(true);
                if (isset($data['success']) && $data['success']) {
                    return redirect()->route('user.group', $group)->with('success', 'Group transaction updated successfully!');
                } else {
                    return back()->withErrors(['message' => $data['message'] ?? 'Failed to update transaction']);
                }
            }
            return $response;
        }

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
        // If it's a group transaction, use the group logic
        if ($transaction->group_id) {
            $group = Group::findOrFail($transaction->group_id);
            $response = app(GroupController::class)->deleteTransaction($group, $transaction);
            
            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $data = $response->getData(true);
                if (isset($data['success']) && $data['success']) {
                    return redirect()->route('user.group', $group)->with('success', 'Group transaction deleted successfully!');
                } else {
                    return back()->withErrors(['message' => $data['message'] ?? 'Failed to delete transaction']);
                }
            }
            return $response;
        }

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


