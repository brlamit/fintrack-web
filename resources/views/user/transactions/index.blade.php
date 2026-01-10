@extends('layouts.user')

@section('title', 'My Transactions')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Transactions</h2>
        <a href="{{ route('user.transactions.create') }}" class="btn btn-primary" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); border: none;">
            <i class="fas fa-plus-circle"></i> Add Transaction
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Income</p>
                    <h4 class="text-success mb-0">${{ number_format($totals['income'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Expense</p>
                    <h4 class="text-danger mb-0">${{ number_format($totals['expense'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Net Balance</p>
                    @php $netClass = $totals['net'] >= 0 ? 'text-success' : 'text-danger'; @endphp
                    <h4 class="{{ $netClass }} mb-0">${{ number_format($totals['net'], 2) }}</h4>
                    <small class="text-muted">{{ $periodLabel }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.transactions') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="period" class="form-label" style="color: #111827;">Period</label>
                        <select id="period" name="period" class="form-select" style="background-color: #ffffff; color: #111827; border-color: #d1d5db;">
                            <option value="this_month" {{ $filters['period'] === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ $filters['period'] === 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_week" {{ $filters['period'] === 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="last_week" {{ $filters['period'] === 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="last_30_days" {{ $filters['period'] === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_year" {{ $filters['period'] === 'this_year' ? 'selected' : '' }}>This Year</option>
                            <option value="all_time" {{ $filters['period'] === 'all_time' ? 'selected' : '' }}>All Time</option>
                            <option value="custom" {{ $filters['period'] === 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                        <small class="text-muted d-block mt-1" style="color: #6b7280 !important;">Select "Custom Range" to specify dates</small>
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="from" class="form-label" style="color: #111827;">From</label>
                        <input id="from" type="date" name="from" class="form-control" value="{{ $filters['from'] }}" {{ $filters['period'] === 'custom' ? '' : 'disabled' }} style="cursor: {{ $filters['period'] === 'custom' ? 'pointer' : 'not-allowed' }}; background-color: {{ $filters['period'] === 'custom' ? '#ffffff' : '#f5f5f5' }}; color: #111827; border-color: #d1d5db;">
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="to" class="form-label" style="color: #111827;">To</label>
                        <input id="to" type="date" name="to" class="form-control" value="{{ $filters['to'] }}" {{ $filters['period'] === 'custom' ? '' : 'disabled' }} style="cursor: {{ $filters['period'] === 'custom' ? 'pointer' : 'not-allowed' }}; background-color: {{ $filters['period'] === 'custom' ? '#ffffff' : '#f5f5f5' }}; color: #111827; border-color: #d1d5db;">
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="type" class="form-label" style="color: #111827;">Type</label>
                        <select id="type" name="type" class="form-select" style="background-color: #ffffff; color: #111827; border-color: #d1d5db;">
                            <option value="" {{ $filters['type'] === null ? 'selected' : '' }}>All</option>
                            <option value="income" {{ $filters['type'] === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ $filters['type'] === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    @php $perPageOptions = [10, 15, 25, 50, 100]; @endphp
                    <div class="col-6 col-md-2">
                        <label for="per_page" class="form-label" style="color: #111827;">Per Page</label>
                        <select id="per_page" name="per_page" class="form-select" style="background-color: #ffffff; color: #111827; border-color: #d1d5db;">
                            @foreach($perPageOptions as $size)
                                <option value="{{ $size }}" {{ (int) $filters['per_page'] === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-3 col-xl-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Apply</button>
                        <a href="{{ route('user.transactions') }}" class="btn btn-outline-secondary" title="Reset filters">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $dateToShow = $transaction->transaction_date ?? $transaction->created_at;
                            $isIncome = $transaction->type === 'income';
                        @endphp
                        <tr>
                            <td>
                                <span class="badge {{ $isIncome ? 'bg-success' : 'bg-danger' }} text-uppercase">{{ $transaction->type ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if($transaction->category)
                                    <span class="badge bg-secondary">{{ $transaction->category->name }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="{{ $isIncome ? 'text-success' : 'text-danger' }}">${{ number_format($transaction->amount, 2) }}</strong>
                            </td>
                            <td>{{ $dateToShow?->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('user.transaction.show', $transaction) }}" class="btn btn-sm btn-outline-info rounded-pill px-3" title="View details">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal" 
                                            data-transaction-id="{{ $transaction->id }}"
                                            data-description="{{ $transaction->description }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-category-id="{{ $transaction->category_id }}"
                                            data-type="{{ $transaction->type }}"
                                            data-date="{{ $dateToShow?->format('Y-m-d') }}"
                                            data-receipt-url="{{ $transaction->receipt?->url }}"
                                            title="Quick Edit">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            data-transaction-id="{{ $transaction->id }}"
                                            data-description="{{ $transaction->description }}"
                                            data-amount="{{ $transaction->amount }}"
                                            data-type="{{ $transaction->type }}"
                                            data-category="{{ $transaction->category->name ?? 'None' }}"
                                            data-date="{{ $dateToShow?->format('M d, Y') }}"
                                            data-group-id="{{ $transaction->group_id }}"
                                            title="Delete">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No transactions yet. <a href="{{ route('user.transactions.create') }}">Add your first transaction</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->links() }}
</div>
    @endif
</div>

<!-- Modals -->
@include('user.transactions.partials._edit_modal')
@include('user.transactions.partials._delete_modal')

<style>
    /* Fix modal positioning to prevent overlap with header */
        .modal {
            z-index: 1050 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
            position: fixed !important;
        }

        .modal.show {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-dialog {
            margin: auto !important;
            max-height: 90vh;
            position: relative !important;
            z-index: 1050 !important;
        }

        .modal-dialog-centered {
            display: flex !important;
            align-items: center !important;
            min-height: 100vh !important;
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3) !important;
            border: none !important;
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden !important;
        }

        /* Additional styling for modal visibility */
        .modal-header {
            border-bottom: 1px solid #e9ecef;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
        }

        /* Ensure form controls are visible in modals */
        .modal-body .form-control,
        .modal-body .form-select {
            background-color: #fff !important;
            color: #212529 !important;
            border: 1px solid #dee2e6 !important;
        }

        .modal-body .form-control:focus,
        .modal-body .form-select:focus {
            background-color: #fff !important;
            color: #212529 !important;
            border-color: #14b8a6 !important;
            box-shadow: 0 0 0 0.2rem rgba(20, 184, 166, 0.25) !important;
        }

        .modal-body .form-label {
            color: #212529 !important;
            font-weight: 600 !important;
        }
    </style>
</div>

@endsection

@push('scripts')
    <script>
        // Make sure the script runs after DOM is fully loaded
        function initializeDateInputs() {
            const periodSelect = document.getElementById('period');
            const fromInput = document.getElementById('from');
            const toInput = document.getElementById('to');

            if (!periodSelect || !fromInput || !toInput) {
                console.error('Date input elements not found');
                return;
            }

            const toggleDateInputs = () => {
                const isCustom = periodSelect.value === 'custom';
                console.log('Period selected:', periodSelect.value, 'Enable date inputs:', isCustom);
                
                if (isCustom) {
                    fromInput.disabled = false;
                    toInput.disabled = false;
                    fromInput.classList.remove('disabled');
                    toInput.classList.remove('disabled');
                } else {
                    fromInput.disabled = true;
                    toInput.disabled = true;
                    fromInput.classList.add('disabled');
                    toInput.classList.add('disabled');
                }
            };

            // Listen to period select changes
            periodSelect.addEventListener('change', toggleDateInputs);
            
            // Initial toggle based on current selection
            toggleDateInputs();
        }

        // Run when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeDateInputs);
        } else {
            initializeDateInputs();
        }

        // Handle Edit Modal
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const transactionId = button.getAttribute('data-transaction-id');
                    const description = button.getAttribute('data-description');
                    const amount = button.getAttribute('data-amount');
                    const categoryId = button.getAttribute('data-category-id');
                    const type = button.getAttribute('data-type');
                    const date = button.getAttribute('data-date');
                    const receiptUrl = button.getAttribute('data-receipt-url');

                    // Update form fields
                    const descInput = document.getElementById('editDescription');
                    const amountInput = document.getElementById('editAmount');
                    const categorySelect = document.getElementById('editCategory');
                    const dateInput = document.getElementById('editDate');
                    const incomeRadio = document.getElementById('editIncome');
                    const expenseRadio = document.getElementById('editExpense');
                    const receiptPreview = document.getElementById('editReceiptPreview');
                    const receiptImage = document.getElementById('editReceiptImage');

                    if (descInput) descInput.value = description || '';
                    if (amountInput) amountInput.value = amount || '';
                    if (categorySelect) categorySelect.value = categoryId || '';
                    if (dateInput) dateInput.value = date || '';

                    // Set type radio button
                    if (type === 'income' && incomeRadio) {
                        incomeRadio.checked = true;
                    } else if (expenseRadio) {
                        expenseRadio.checked = true;
                    }

                    // Handle Receipt Preview
                    if (receiptUrl && receiptPreview && receiptImage) {
                        receiptImage.src = receiptUrl;
                        receiptPreview.classList.remove('d-none');
                    } else if (receiptPreview) {
                        receiptPreview.classList.add('d-none');
                    }

                    // Update form action URL
                    const form = document.getElementById('editTransactionForm');
                    if (form) {
                        form.action = `/transactions/${transactionId}`;
                    }
                });
            }

            // Handle Delete Modal
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const transactionId = button.getAttribute('data-transaction-id');
                    const description = button.getAttribute('data-description');
                    const amount = button.getAttribute('data-amount');
                    const type = button.getAttribute('data-type');
                    const date = button.getAttribute('data-date');
                    const category = button.getAttribute('data-category');
                    const groupId = button.getAttribute('data-group-id');

                    // Update delete modal content
                    const typeEl = document.getElementById('deleteType');
                    const amountEl = document.getElementById('deleteAmount');
                    const categoryEl = document.getElementById('deleteCategory');
                    const dateEl = document.getElementById('deleteDate');
                    const descEl = document.getElementById('deleteDescription');
                    const groupWarning = document.getElementById('groupDeleteWarning');

                    if (typeEl) typeEl.textContent = type ? type.charAt(0).toUpperCase() + type.slice(1) : 'N/A';
                    if (amountEl) amountEl.textContent = '$' + parseFloat(amount || 0).toFixed(2);
                    if (categoryEl) categoryEl.textContent = category || 'N/A';
                    if (dateEl) dateEl.textContent = date || 'N/A';
                    if (descEl) descEl.textContent = description || 'N/A';
                    
                    if (groupWarning) {
                        if (groupId && groupId !== 'null' && groupId !== '') {
                            groupWarning.classList.remove('d-none');
                        } else {
                            groupWarning.classList.add('d-none');
                        }
                    }

                    // Update form action URL
                    const form = document.getElementById('deleteTransactionForm');
                    if (form) {
                        form.action = `/transactions/${transactionId}`;
                    }
                });
            }
        });
    </script>
@endpush
