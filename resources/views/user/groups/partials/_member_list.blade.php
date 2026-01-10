<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">Member Contributions</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Member</th>
                        <th scope="col" class="text-center">Role</th>
                        <th scope="col" class="text-center">Joined</th>
                        <th scope="col" class="text-end">Income</th>
                        <th scope="col" class="text-end">Expense</th>
                        <th scope="col" class="text-center">Transactions</th>
                        <th scope="col" class="text-end">Balance</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $isAdmin = $group->members->firstWhere('user_id', auth()->id())->role === 'admin';
                        $currency = fn (float $value) => '$' . number_format($value, 2);
                    @endphp
                    @forelse($group->members as $member)
                        @php
                            $stats = $memberStats->get($member->user_id);
                            $income = $stats->income_total ?? 0;
                            $expense = $stats->expense_total ?? 0;
                            $balance = $income - $expense;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $member->user->name }}</div>
                                <div class="text-muted small">{{ $member->user->email }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ ucfirst($member->role) }}</span>
                            </td>
                            <td class="text-center text-muted small">{{ optional($member->joined_at)->format('M d, Y') ?? '—' }}</td>
                            <td class="text-end text-success">{{ $currency($income) }}</td>
                            <td class="text-end text-danger">{{ $currency($expense) }}</td>
                            <td class="text-center">{{ number_format($stats->transactions_count ?? 0) }}</td>
                            <td class="text-end {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">{{ $currency($balance) }}</td>
                            <td class="text-end">
                                @if($isAdmin && $member->user_id !== $group->owner_id && $member->user_id !== auth()->id())
                                    <form action="{{ route('groups.member.remove', ['group' => $group->id, 'member' => $member->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove {{ $member->user->name }} from this group?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                    </form>
                                @elseif($member->user_id === $group->owner_id)
                                    <span class="badge bg-light text-dark">Owner</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No members have joined this group yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
