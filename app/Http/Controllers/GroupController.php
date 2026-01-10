<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Transaction;
use App\Models\User;
use App\Mail\GroupInvitationMail;
use App\Services\ReceiptOcrService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function __construct(private readonly ReceiptOcrService $receiptOcrService)
    {
    }
    /**
     * Display a listing of the user's groups.
     */
    public function index(Request $request): JsonResponse
    {
        $groups = auth()->user()->groups()
            ->with(['owner', 'members'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    /**
     * Store a newly created group.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:family,friends',
            'budget_limit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'budget_limit' => $request->budget_limit,
            'owner_id' => auth()->id(),
            'invite_code' => Str::random(8),
        ]);

        // Add owner as admin member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Group created successfully',
                'data' => $group->load(['owner', 'members']),
            ], 201);
        }

        return redirect()->route('user.group', $group)
            ->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group): JsonResponse
    {
        // Check if user is member of the group
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group->load(['owner', 'members.user']),
        ]);
    }

    /**
     * Update the specified group.
     */
    public function update(Request $request, Group $group)
    {
        // Check if user is admin of the group
        $member = $group->members()->where('user_id', auth()->id())->first();
        if (!$member || $member->role !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:family,friends',
            'description' => 'nullable|string|max:1000',
            'budget_limit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $group->update($request->only(['name', 'type', 'description', 'budget_limit']));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Group updated successfully',
                'data' => $group->load(['owner', 'members']),
            ]);
        }

        return redirect()->route('user.group', $group)->with('success', 'Group updated successfully');
    }

    /**
     * Remove the specified group.
     */
    public function destroy(Request $request, Group $group)
    {
        // Check if user is owner of the group
        if ($group->owner_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            return redirect()->back()->withErrors(['error' => 'You are not allowed to delete this group.']);
        }

        $group->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully',
            ]);
        }

        return redirect()->route('user.groups')->with('success', 'Group deleted successfully.');
    }

    /**
     * Invite a user to the group.
     */
    public function invite(Request $request, Group $group): JsonResponse
    {
        // Check if user is admin of the group
        $member = $group->members()->where('user_id', auth()->id())->first();
        if (!$member || $member->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Check if user is already a member
        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member of this group',
            ], 422);
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        // Send invitation email to existing user
        try {
            $invitationMail = new GroupInvitationMail($user, $group, auth()->user());
            Mail::to($user->email)->send($invitationMail);
        } catch (\Exception $e) {
            \Log::error('Failed to send group invitation email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'User invited to group successfully',
        ]);
    }

    /**
     * Get group members.
     */
    public function members(Group $group): JsonResponse
    {
        // Check if user is member of the group
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        $members = $group->members()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

    /**
     * Get group transactions as JSON for API consumers.
     */
    public function transactions(Request $request, Group $group): JsonResponse
    {
        // Check if user is member of the group
        if (! $group->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        $transactions = $group->sharedTransactions()
            ->with(['user', 'category'])
            ->orderByDesc(DB::raw('COALESCE(transaction_date, created_at)'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'group' => $group,
                'transactions' => $transactions,
            ],
        ]);
    }

    /**
     * Split an expense among group members.
     */
    public function splitExpense(Request $request, Group $group)
    {
        if (! $group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'amount' => 'nullable|required_without:receipt|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'split_type' => 'required|in:equal,custom,percentage',
            'splits' => 'required|array|min:1',
            'splits.*.user_id' => 'required|exists:users,id',
            'splits.*.amount' => 'nullable|numeric|min:0',
            'splits.*.percent' => 'nullable|numeric|min:0|max:100',
            'receipt' => 'nullable|file|image|max:5120',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $members = $group->members()->with('user')->get();
        $memberIds = $members->pluck('user_id')->all();

        $handleError = function (string $message) use ($request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->withErrors(['split' => $message])->withInput();
        };

        try {
            $receiptId = null;
            $receipt = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                $filename = time() . '_' . $file->getClientOriginalName();
                $disk = env('FILESYSTEM_DISK', config('filesystems.default'));
                $path = $file->storeAs('receipts/' . auth()->id(), $filename, $disk);

                // Build a public URL for storage entry when possible (include bucket and encode)
                $pathToSave = $path;
                $generated = null;
                try {
                    $generated = \Storage::disk($disk)->url($path);
                } catch (\Throwable $e) {
                    $generated = null;
                }

                $diskConfig = config("filesystems.disks.{$disk}", []);
                $diskUrl = $diskConfig['url'] ?? null;
                $bucket = $diskConfig['bucket'] ?? env('SUPABASE_PUBLIC_BUCKET');

                if (!empty($generated) && !empty($bucket) && strpos($generated, trim($bucket, '/')) === false) {
                    $generated = null;
                }

                if (empty($generated) && !empty($diskUrl)) {
                    $encodedKey = implode('/', array_map('rawurlencode', explode('/', $path)));
                    if (!empty($bucket)) {
                        $generated = rtrim($diskUrl, '/') . '/' . trim($bucket, '/') . '/' . ltrim($encodedKey, '/');
                    } else {
                        $generated = rtrim($diskUrl, '/') . '/' . ltrim($encodedKey, '/');
                    }
                }

                if (!empty($generated)) {
                    $pathToSave = $generated;
                }

                $receipt = \App\Models\Receipt::create([
                    'user_id' => auth()->id(),
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'path' => $pathToSave,
                    'size' => $file->getSize(),
                    'processed' => false,
                ]);
                // OCR the receipt so parsed JSON is available for group transactions
                $this->receiptOcrService->process($receipt);
                $receiptId = $receipt->id;
            }

            $type = $request->input('type', 'expense');
            $categoryId = $request->input('category_id');

            if (! $categoryId) {
                if ($type === 'income') {
                    $other = \App\Models\Category::firstOrCreate(
                        ['user_id' => null, 'name' => 'Other Income', 'type' => 'income'],
                        ['icon' => '💰', 'color' => '#38B2AC']
                    );
                } else {
                    $other = \App\Models\Category::firstOrCreate(
                        ['user_id' => null, 'name' => 'Other Expense', 'type' => 'expense'],
                        ['icon' => '📦', 'color' => '#9E9E9E']
                    );
                }
                $categoryId = $other->id;
            }

            $splitType = $request->input('split_type');

            $amountInput = $request->input('amount');
            $amount = $amountInput !== null && $amountInput !== ''
                ? (float) $amountInput
                : 0.0;

            // If amount not provided but we have a receipt with OCR data, try to use the estimated total
            if ($amount <= 0.0 && $receipt) {
                $receipt->refresh();
                $parsed = $receipt->parsed_data ?? [];
                $estimatedTotal = is_array($parsed) && array_key_exists('estimated_total', $parsed)
                    ? (float) ($parsed['estimated_total'] ?? 0)
                    : 0.0;

                if ($estimatedTotal > 0.0) {
                    $amount = $estimatedTotal;
                }
            }

            if ($amount <= 0.0) {
                return $handleError('Amount is required when a valid total cannot be read from the receipt.');
            }
            $rawSplits = $request->input('splits', []);
            $computedSplits = [];

            $ensureMember = function ($userId) use ($memberIds, $handleError) {
                if (! in_array($userId, $memberIds, true)) {
                    throw new \RuntimeException('Invalid member included in split.');
                }
            };

            if ($splitType === 'equal') {
                $count = max($members->count(), 1);
                $base = round($amount / $count, 2);
                $runningTotal = 0.0;

                foreach ($members as $index => $member) {
                    $portion = $index === $count - 1
                        ? round($amount - $runningTotal, 2)
                        : $base;

                    $runningTotal += $portion;
                    $computedSplits[] = [
                        'user_id' => $member->user_id,
                        'amount' => max($portion, 0),
                    ];
                }
            } elseif ($splitType === 'percentage') {
                $percentTotal = 0.0;
                foreach ($rawSplits as $index => $split) {
                    $userId = (int) ($split['user_id'] ?? 0);
                    $ensureMember($userId);

                    if (! isset($split['percent'])) {
                        throw new \RuntimeException('Percentage value missing for one of the members.');
                    }

                    $percent = (float) $split['percent'];
                    $percentTotal += $percent;
                    $computedSplits[] = [
                        'user_id' => $userId,
                        'amount' => round(($percent / 100) * $amount, 2),
                        'percent' => $percent,
                    ];
                }

                if ($percentTotal <= 0 || abs($percentTotal - 100) > 0.01) {
                    throw new \RuntimeException('Percentage splits must add up to 100%.');
                }

                $assigned = array_sum(array_column($computedSplits, 'amount'));
                if (abs($assigned - $amount) > 0.01 && count($computedSplits) > 0) {
                    $diff = round($amount - $assigned, 2);
                    $computedSplits[count($computedSplits) - 1]['amount'] += $diff;
                }
            } else {
                $customSplits = [];
                foreach ($rawSplits as $split) {
                    $userId = (int) ($split['user_id'] ?? 0);
                    $ensureMember($userId);

                    if (! isset($split['amount'])) {
                        throw new \RuntimeException('Amount missing for one of the members.');
                    }

                    $customSplits[] = [
                        'user_id' => $userId,
                        'amount' => round((float) $split['amount'], 2),
                    ];
                }

                $assigned = array_sum(array_column($customSplits, 'amount'));
                if (abs($assigned - $amount) > 0.01) {
                    throw new \RuntimeException('Custom split amounts must add up to the total.');
                }

                $computedSplits = $customSplits;
            }

            if (empty($computedSplits)) {
                return $handleError('No split data provided.');
            }

            foreach ($computedSplits as $split) {
                \App\Models\Transaction::create([
                    'user_id' => $split['user_id'],
                    'group_id' => $group->id,
                    'category_id' => $categoryId,
                    'amount' => $split['amount'],
                    'description' => $request->input('description'),
                    'transaction_date' => now()->toDateString(),
                    'type' => $type,
                    'receipt_id' => $receiptId,
                ]);
            }

            if ($type === 'expense' && ! is_null($group->budget_limit)) {
                $group->budget_limit = max(0, round($group->budget_limit - $amount, 2));
                $group->save();
            }

            $message = $type === 'income'
                ? 'Income recorded and split successfully'
                : 'Expense recorded and split successfully';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\RuntimeException $validationException) {
            return $handleError($validationException->getMessage());
        } catch (\Exception $e) {
            \Log::error('Failed to create group transaction split: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create split transaction',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to create split transaction');
        }
    }

    /**
     * Update a group transaction.
     */
    public function updateTransaction(Request $request, Group $group, \App\Models\Transaction $transaction)
    {
        // Check if user is member of the group
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Group not found',
                ], 404);
            }
            return redirect()->back()->with('error', 'Group not found');
        }

        // Check if transaction belongs to this group
        if ($transaction->group_id !== $group->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found in this group',
                ], 404);
            }
            return redirect()->back()->with('error', 'Transaction not found in this group');
        }

        // Check authorization: user must be either the creator or an admin
        $member = $group->members()->where('user_id', auth()->id())->first();
        $isCreator = $transaction->user_id === auth()->id();
        $isAdmin = $member && $member->role === 'admin';
        
        if (!$isCreator && !$isAdmin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only update your own transactions.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized. You can only update your own transactions.');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'sometimes|date|before_or_equal:today',
            'type' => 'sometimes|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'split_type' => 'required|in:equal,custom,percentage',
            'splits' => 'required|array|min:1',
            'splits.*.user_id' => 'required|exists:users,id',
            'splits.*.amount' => 'nullable|numeric|min:0',
            'splits.*.percent' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Find related transactions in the same split group
            $relatedTransactions = \App\Models\Transaction::where('group_id', $transaction->group_id)
                ->where('description', $transaction->description)
                ->where('category_id', $transaction->category_id)
                ->where('type', $transaction->type)
                ->whereDate('created_at', $transaction->created_at->toDateString())
                ->whereBetween('created_at', [
                    $transaction->created_at->subSeconds(2),
                    $transaction->created_at->addSeconds(2)
                ])
                ->get();

            // Total amount for the whole split
            $newTotalAmount = (float) $request->input('amount');
            $splitType = $request->input('split_type');
            $rawSplits = $request->input('splits', []);
            $newType = $request->input('type', $transaction->type);
            $newCategoryId = $request->input('category_id', $transaction->category_id);
            $newDescription = $request->input('description', $transaction->description);
            $newDate = $request->input('transaction_date', $transaction->transaction_date ? $transaction->transaction_date->toDateString() : now()->toDateString());

            // Calculate portions
            $computedSplits = [];
            if ($splitType === 'equal') {
                $count = count($rawSplits);
                $base = round($newTotalAmount / $count, 2);
                $runningTotal = 0.0;
                foreach ($rawSplits as $idx => $s) {
                    $portion = ($idx === $count - 1) ? round($newTotalAmount - $runningTotal, 2) : $base;
                    $runningTotal += $portion;
                    $computedSplits[] = ['user_id' => $s['user_id'], 'amount' => $portion];
                }
            } elseif ($splitType === 'percentage') {
                foreach ($rawSplits as $s) {
                    $computedSplits[] = [
                        'user_id' => $s['user_id'],
                        'amount' => round(($s['percent'] / 100) * $newTotalAmount, 2)
                    ];
                }
            } else { // custom
                foreach ($rawSplits as $s) {
                    $computedSplits[] = ['user_id' => $s['user_id'], 'amount' => (float) $s['amount']];
                }
            }

            // Adjust group budget: reverse old split total, add new split total
            if (!is_null($group->budget_limit)) {
                $oldSplitTotal = $relatedTransactions->where('type', 'expense')->sum('amount');
                $newSplitTotal = ($newType === 'expense') ? $newTotalAmount : 0;
                $group->budget_limit = max(0, round($group->budget_limit + $oldSplitTotal - $newSplitTotal, 2));
                $group->save();
            }

            // Update existing or create new transactions
            $processedUserIds = [];
            foreach ($computedSplits as $split) {
                $userId = $split['user_id'];
                $processedUserIds[] = $userId;
                
                $existing = $relatedTransactions->where('user_id', $userId)->first();
                if ($existing) {
                    $existing->update([
                        'amount' => $split['amount'],
                        'description' => $newDescription,
                        'category_id' => $newCategoryId,
                        'type' => $newType,
                        'transaction_date' => $newDate,
                    ]);
                } else {
                    \App\Models\Transaction::create([
                        'user_id' => $userId,
                        'group_id' => $group->id,
                        'category_id' => $newCategoryId,
                        'amount' => $split['amount'],
                        'description' => $newDescription,
                        'transaction_date' => $newDate,
                        'type' => $newType,
                        'receipt_id' => $transaction->receipt_id,
                    ]);
                }
            }

            // Delete transactions for users no longer in the split
            foreach ($relatedTransactions as $rel) {
                if (!in_array($rel->user_id, $processedUserIds)) {
                    $rel->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group split updated successfully',
                'data' => $transaction->fresh()->load(['user', 'category']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update group transaction: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a group transaction.
     */
    public function deleteTransaction(Group $group, \App\Models\Transaction $transaction): JsonResponse
    {
        // Check if user is member of the group
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        // Check if transaction belongs to this group
        if ($transaction->group_id !== $group->id) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found in this group',
            ], 404);
        }

        // Check authorization: user must be either the creator or an admin
        $member = $group->members()->where('user_id', auth()->id())->first();
        $isCreator = $transaction->user_id === auth()->id();
        $isAdmin = $member && $member->role === 'admin';
        
        if (!$isCreator && !$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete your own transactions.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Find related transactions in the same split group
            $relatedTransactions = \App\Models\Transaction::where('group_id', $transaction->group_id)
                ->where('description', $transaction->description)
                ->where('category_id', $transaction->category_id)
                ->where('type', $transaction->type)
                ->whereDate('created_at', $transaction->created_at->toDateString())
                ->whereBetween('created_at', [
                    $transaction->created_at->subSeconds(2),
                    $transaction->created_at->addSeconds(2)
                ])
                ->get();

            // Total amount to restore to budget
            $restoreAmount = $relatedTransactions->where('type', 'expense')->sum('amount');

            // Restore budget if there's an expense total
            if ($restoreAmount > 0 && !is_null($group->budget_limit)) {
                $group->budget_limit = max(0, round($group->budget_limit + $restoreAmount, 2));
                $group->save();
            }

            // Delete all related transactions
            foreach ($relatedTransactions as $rel) {
                $rel->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Group transaction and its splits deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to delete group transaction: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete group transaction',
            ], 500);
        }
    }
}
