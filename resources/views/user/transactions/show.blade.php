@extends('layouts.user')

@section('title', 'Transaction')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Transaction Details</h3>
            <p class="text-muted small mb-0">{{ $transaction->description ?? '—' }}</p>
        </div>
        <div>
            @if($isAdmin || $transaction->user_id === auth()->id())
                @if($transaction->group_id)
                    <button type="button" class="btn btn-warning me-1 rounded-pill px-3 shadow-sm hover-shadow-lg" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editGroupTransactionModal"
                            data-transaction-id="{{ $transaction->id }}"
                            data-group-id="{{ $transaction->group_id }}"
                            data-total-amount="{{ $relatedTransactions->sum('amount') }}"
                            data-description="{{ $transaction->description }}"
                            data-category-id="{{ $transaction->category_id }}"
                            data-type="{{ $transaction->type }}"
                            data-date="{{ ($transaction->transaction_date ?? $transaction->created_at)->format('Y-m-d') }}">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger me-1 rounded-pill px-3 shadow-sm hover-shadow-lg" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal"
                            data-transaction-id="{{ $transaction->id }}"
                            data-description="{{ $transaction->description }}"
                            data-amount="{{ $relatedTransactions->sum('amount') }}"
                            data-type="{{ $transaction->type }}"
                            data-category="{{ $transaction->category->name ?? 'None' }}"
                            data-date="{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}"
                            data-group-id="{{ $transaction->group_id }}">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                @else
                    <button type="button" class="btn btn-primary me-1 rounded-pill px-3 shadow-sm hover-shadow-lg" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal"
                            data-transaction-id="{{ $transaction->id }}"
                            data-description="{{ $transaction->description }}"
                            data-amount="{{ $transaction->amount }}"
                            data-category-id="{{ $transaction->category_id }}"
                            data-type="{{ $transaction->type }}"
                            data-date="{{ ($transaction->transaction_date ?? $transaction->created_at)->format('Y-m-d') }}"
                            data-receipt-url="{{ $transaction->receipt?->url }}">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger me-1 rounded-pill px-3 shadow-sm hover-shadow-lg" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal"
                            data-transaction-id="{{ $transaction->id }}"
                            data-description="{{ $transaction->description }}"
                            data-amount="{{ $transaction->amount }}"
                            data-type="{{ $transaction->type }}"
                            data-category="{{ $transaction->category->name ?? 'None' }}"
                            data-date="{{ ($transaction->transaction_date ?? $transaction->created_at)->format('M d, Y') }}"
                            data-group-id="{{ $transaction->group_id }}">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                @endif
            @endif
            @if($transaction->group)
                <a href="{{ route('user.group.transactions', $transaction->group) }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            @else
                <a href="{{ route('user.transactions') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <h4 class="mb-2">{{ $transaction->description ?? 'No description' }}</h4>
                    <p class="mb-1 text-muted">Category: {{ $transaction->category->name ?? '—' }}</p>
                    <p class="mb-1 text-muted">Recorded by: {{ $transaction->user->name ?? '—' }}</p>
                    <p class="mb-1 text-muted">Type: <strong class="text-uppercase">{{ $transaction->type }}</strong></p>
                    <p class="mb-1 text-muted">Date: {{ ($transaction->transaction_date ?? $transaction->created_at)?->format('M d, Y') }}</p>
                    @if($transaction->group_id)
                        <p class="mb-1">Your Share: <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">${{ number_format($transaction->amount, 2) }}</strong></p>
                        <p class="mb-1 text-muted">Total Group Amount: <strong class="text-dark">${{ number_format($relatedTransactions->sum('amount'), 2) }}</strong></p>
                    @else
                        <p class="mb-1">Amount: <strong class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">${{ number_format($transaction->amount, 2) }}</strong></p>
                    @endif
                    
                    @if($transaction->group_id && $relatedTransactions->count() > 1)
                        <div class="mt-4">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-users me-2 text-primary"></i>Team Split Breakdown
                                <span class="badge bg-light text-dark ms-2 fw-normal">Total: ${{ number_format($relatedTransactions->sum('amount'), 2) }}</span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle">
                                    <thead class="text-muted small text-uppercase">
                                        <tr>
                                            <th>Member</th>
                                            <th class="text-end">Share</th>
                                            <th class="text-end">Percent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalAmount = $relatedTransactions->sum('amount'); @endphp
                                        @foreach($relatedTransactions as $rel)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <i class="fas fa-user text-muted small"></i>
                                                        </div>
                                                        <span>{{ $rel->user->name }}</span>
                                                        @if($rel->user_id === $transaction->user_id)
                                                            <span class="badge bg-primary-subtle text-primary ms-2" style="font-size: 0.65rem;">YOU</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-end fw-semibold text-dark">${{ number_format($rel->amount, 2) }}</td>
                                                <td class="text-end text-muted small">
                                                    {{ $totalAmount > 0 ? round(($rel->amount / $totalAmount) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($transaction->tags)
                        <p class="mb-1 text-muted">Tags: {{ implode(', ', (array) $transaction->tags) }}</p>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($transaction->receipt)
                        @php
                            $img = $transaction->receipt->url ?? null;
                        @endphp
                        @if($img)
                            <img src="{{ $img }}" alt="Receipt" class="img-fluid rounded" style="max-height:260px; object-fit:cover;">
                        @else
                            <div class="text-muted small">Receipt not available.</div>
                        @endif
                    @else
                        <div class="text-muted small">No receipt attached.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin || $transaction->user_id === auth()->id())
        @if(!$transaction->group_id)
            @include('user.transactions.partials._edit_modal')
        @else
            @include('user.groups.partials._edit_transaction_modal', ['group' => $transaction->group])
        @endif

        @include('user.transactions.partials._delete_modal')

    @endif
</div>

@push('styles')
<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}

.hover-shadow-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.rounded-pill {
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}

.modal-content {
    border: none !important;
}

.form-control, .form-select {
    border-radius: 8px !important;
}

.input-group-text {
    border-radius: 8px 0 0 8px !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Standard/Simple Transaction Edit Modal Logic
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

            if (type === 'income' && incomeRadio) {
                incomeRadio.checked = true;
            } else if (expenseRadio) {
                expenseRadio.checked = true;
            }

            if (receiptUrl && receiptPreview && receiptImage) {
                receiptImage.src = receiptUrl;
                receiptPreview.classList.remove('d-none');
            } else if (receiptPreview) {
                receiptPreview.classList.add('d-none');
            }

            const form = document.getElementById('editTransactionForm');
            if (form) {
                form.action = `/transactions/${transactionId}`;
            }
        });
    }

    // 2. Group Transaction Edit Logic
    const groupEditForm = document.getElementById('edit-group-expense-form');
    if (groupEditForm) {
        const editTotalInput = document.getElementById('edit-total-amount');
        const editSplitTypeInputs = groupEditForm.querySelectorAll('input[name="split_type"]');
        const editAutoDistributeBtn = document.getElementById('edit-auto-distribute');
        const editAmountInputs = groupEditForm.querySelectorAll('.edit-split-amount');
        const editPercentInputs = groupEditForm.querySelectorAll('.edit-split-percent');
        const editAmountWrappers = groupEditForm.querySelectorAll('.edit-split-input-amount');
        const editPercentWrappers = groupEditForm.querySelectorAll('.edit-split-input-percent');

        function updateEditSplitVisibility() {
            const selectedType = groupEditForm.querySelector('input[name="split_type"]:checked')?.value;
            if (!selectedType) return;
            
            if (selectedType === 'percentage') {
                editAmountWrappers.forEach(w => w.classList.add('d-none'));
                editPercentWrappers.forEach(w => w.classList.remove('d-none'));
            } else {
                editAmountWrappers.forEach(w => w.classList.remove('d-none'));
                editPercentWrappers.forEach(w => w.classList.add('d-none'));
            }
        }

        function distributeEditSplits() {
            const total = parseFloat(editTotalInput.value) || 0;
            const selectedType = groupEditForm.querySelector('input[name="split_type"]:checked')?.value;
            const count = editAmountInputs.length;

            if (count === 0) return;

            if (selectedType === 'equal') {
                const share = (total / count).toFixed(2);
                editAmountInputs.forEach(input => input.value = share);
            } else if (selectedType === 'percentage') {
                const share = (100 / count).toFixed(2);
                editPercentInputs.forEach(input => input.value = share);
            }
        }

        editSplitTypeInputs.forEach(input => {
            input.addEventListener('change', () => {
                updateEditSplitVisibility();
                if (input.value === 'equal') distributeEditSplits();
            });
        });

        if (editAutoDistributeBtn) {
            editAutoDistributeBtn.addEventListener('click', distributeEditSplits);
        }

        editTotalInput.addEventListener('input', () => {
            const selectedType = groupEditForm.querySelector('input[name="split_type"]:checked')?.value;
            if (selectedType === 'equal') distributeEditSplits();
        });

        updateEditSplitVisibility();
    }

    // 3. Handle Delete Modal
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

            if (typeEl) typeEl.textContent = type ? type.charAt(0).toUpperCase() + type.slice(1) : '—';
            if (amountEl) amountEl.textContent = '$' + parseFloat(amount || 0).toFixed(2);
            if (categoryEl) categoryEl.textContent = category || '—';
            if (dateEl) dateEl.textContent = date || '—';
            if (descEl) descEl.textContent = description || '—';
            
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
@endsection
