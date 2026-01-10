<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Recent Transactions</h5>
        <div>
            @if(!empty($hasMoreTransactions))
                <a href="{{ route('user.group.transactions', $group) }}" class="btn btn-sm btn-outline-secondary">View All Transactions</a>
            @else
                <a href="{{ route('user.group.transactions', $group) }}" class="btn btn-sm btn-outline-secondary">View All</a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Description</th>
                        <th scope="col">Type</th>
                        <th scope="col">Receipt</th>
                        <th scope="col">Recorded By</th>
                        <th scope="col">Category</th>
                        <th scope="col" class="text-end">Amount</th>
                        <th scope="col" class="text-end">Date</th>
                        <th scope="col"> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        @php
                            $isIncome = $transaction->type === 'income';
                            $date = $transaction->transaction_date ?? $transaction->created_at;
                            $currency = fn (float $value) => '$' . number_format($value, 2);
                        @endphp
                        <tr>
                            <td>{{ $transaction->description ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $isIncome ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                    {{ $isIncome ? 'Income' : 'Expense' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $imgUrl = null;
                                    $path = $transaction->receipt_path ?? null;

                                    if ($path) {
                                        $disk = $transaction->receipt_disk ?? config('filesystems.default');

                                        // If the path already looks like a URL, use it directly
                                        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) || filter_var($path, FILTER_VALIDATE_URL)) {
                                            $imgUrl = $path;
                                        } else {
                                            try {
                                                // Prefer Storage->url when the file exists on the configured disk
                                                if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($path)) {
                                                    $imgUrl = \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
                                                } else {
                                                    $imgUrl = null;
                                                }
                                            } catch (\Throwable $e) {
                                                $imgUrl = null;
                                            }
                                        }
                                    }
                                @endphp

                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" width="80" height="60" style="object-fit: cover; border-radius: 4px;" alt="{{ $transaction->description ?? 'Receipt Image' }}">
                                @else
                                    <span class="text-muted small">No Image</span>
                                @endif
                            </td>
                            <td>{{ $transaction->user->name ?? '—' }}</td>
                            <td>{{ $transaction->category->name ?? '—' }}</td>
                            <td class="text-end {{ $isIncome ? 'text-success' : 'text-danger' }}">{{ $currency($transaction->amount) }}</td>
                            <td class="text-end text-muted small">{{ $date?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('user.transaction.show', $transaction) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No transactions recorded for this group yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
