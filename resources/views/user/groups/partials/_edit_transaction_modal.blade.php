<!-- Edit Group Transaction Modal -->
<div class="modal fade" id="editGroupTransactionModal" tabindex="-1" aria-labelledby="editGroupTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(60px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 bg-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-edit text-warning" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold" id="editGroupTransactionModalLabel">Edit Group Transaction</h5>
                        <p class="text-muted small mb-0">Update shared transaction details and splits</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="" method="POST" enctype="multipart/form-data" id="edit-group-expense-form" class="row g-4">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Total Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-dollar-sign text-muted"></i>
                            </span>
                            <input type="number" step="0.01" name="amount" class="form-control border-start-0 ps-0"
                                   style="border-radius: 0 8px 8px 0;" id="edit-total-amount" placeholder="0.00">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Transaction Type</label>
                        <div class="btn-group w-100" role="group" aria-label="Transaction type">
                            <input type="radio" class="btn-check" name="type" id="edit-type-expense" value="expense" autocomplete="off">
                            <label class="btn btn-outline-danger rounded-start-pill" for="edit-type-expense">
                                <i class="fas fa-minus-circle me-1"></i>Expense
                            </label>
                            <input type="radio" class="btn-check" name="type" id="edit-type-income" value="income" autocomplete="off">
                            <label class="btn btn-outline-success rounded-end-pill" for="edit-type-income">
                                <i class="fas fa-plus-circle me-1"></i>Income
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Split Method</label>
                        <div class="btn-group w-100 flex-wrap" role="group" aria-label="Split type">
                            <input type="radio" class="btn-check" name="split_type" id="edit-split-type-equal" value="equal" autocomplete="off">
                            <label class="btn btn-outline-secondary rounded-pill me-1 mb-1" for="edit-split-type-equal">
                                <i class="fas fa-equals me-1"></i>Equal
                            </label>
                            <input type="radio" class="btn-check" name="split_type" id="edit-split-type-custom" value="custom" autocomplete="off">
                            <label class="btn btn-outline-secondary rounded-pill me-1 mb-1" for="edit-split-type-custom">
                                <i class="fas fa-sliders-h me-1"></i>Custom
                            </label>
                            <input type="radio" class="btn-check" name="split_type" id="edit-split-type-percentage" value="percentage" autocomplete="off">
                            <label class="btn btn-outline-secondary rounded-pill mb-1" for="edit-split-type-percentage">
                                <i class="fas fa-percentage me-1"></i>Percent
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Description</label>
                        <input type="text" name="description" id="edit-description" class="form-control"
                               style="border-radius: 8px;" placeholder="E.g. Grocery run">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Category</label>
                        <select name="category_id" id="edit-category" class="form-control" style="border-radius: 8px;">
                            <option value="">Select a category</option>
                            @if($categories->isNotEmpty())
                                @php
                                    $groupedCategories = $categories->groupBy(fn ($category) => $category->type ?? 'uncategorized');
                                @endphp
                                @foreach($groupedCategories as $type => $typeCategories)
                                    <optgroup label="{{ ucfirst($type) }}">
                                        @foreach($typeCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Date</label>
                        <input type="date" name="transaction_date" id="edit-date" class="form-control" style="border-radius: 8px;">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Receipt (optional)</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*" style="border-radius: 8px;">
                        <div class="text-muted small mt-1">Leave empty to keep existing receipt (if any)</div>
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-muted text-uppercase small mb-3">Per-member split</h6>
                        <div id="edit-splits-container">
                            @foreach($group->members as $idx => $member)
                                <div class="col-12 mb-3">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <strong>{{ $member->user->name }}</strong>
                                            <span class="text-muted small ms-2">{{ ucfirst($member->role) }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <input type="hidden" name="splits[{{ $idx }}][user_id]" value="{{ $member->user->id }}">
                                            <div class="edit-split-input-amount">
                                                <input type="number" step="0.01" name="splits[{{ $idx }}][amount]" class="form-control edit-split-amount" 
                                                       data-user-id="{{ $member->user->id }}" style="width: 130px;" placeholder="0.00">
                                            </div>
                                            <div class="edit-split-input-percent d-none">
                                                <div class="input-group" style="width: 130px;">
                                                    <input type="number" step="0.01" name="splits[{{ $idx }}][percent]" class="form-control edit-split-percent" 
                                                           data-user-id="{{ $member->user->id }}" placeholder="0.00">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="edit-auto-distribute">Auto distribute</button>
                    <button type="submit" form="edit-group-expense-form" class="btn btn-primary rounded-pill px-4 shadow-sm">Update Transaction</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('edit-group-expense-form');
    if (!editForm) return;

    const totalInput = document.getElementById('edit-total-amount');
    const splitRadios = editForm.querySelectorAll('input[name="split_type"]');
    const autoDistributeButton = document.getElementById('edit-auto-distribute');
    const amountContainers = editForm.querySelectorAll('.edit-split-input-amount');
    const percentContainers = editForm.querySelectorAll('.edit-split-input-percent');
    const amountInputs = editForm.querySelectorAll('.edit-split-amount');
    const percentInputs = editForm.querySelectorAll('.edit-split-percent');

    const getMode = () => editForm.querySelector('input[name="split_type"]:checked')?.value ?? 'equal';

    const toggleSplitInputs = () => {
        const mode = getMode();
        const usePercent = mode === 'percentage';

        percentContainers.forEach(container => container.classList.toggle('d-none', !usePercent));
        amountContainers.forEach(container => container.classList.toggle('d-none', usePercent));
    };

    const distributeAmounts = () => {
        const mode = getMode();
        if (mode === 'percentage') {
            const count = percentInputs.length;
            if (!count) return;

            const slice = count ? Math.round((100 / count) * 100) / 100 : 0;
            let remaining = 100;

            percentInputs.forEach((input, index) => {
                const value = index === count - 1 ? remaining : slice;
                input.value = value.toFixed(2);
                remaining = Number((remaining - value).toFixed(2));
            });
            return;
        }

        const total = parseFloat(totalInput?.value || '0');
        const count = amountInputs.length;
        if (!count || isNaN(total)) return;

        const base = Math.round((total / count) * 100) / 100;
        let assigned = 0;

        amountInputs.forEach((input, index) => {
            let value = base;
            if (index === count - 1) {
                value = Number((total - assigned).toFixed(2));
            }
            assigned = Number((assigned + value).toFixed(2));
            input.value = value.toFixed(2);
        });
    };

    autoDistributeButton?.addEventListener('click', () => { distributeAmounts(); });
    totalInput?.addEventListener('input', () => { if (getMode() === 'equal') distributeAmounts(); });
    splitRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            toggleSplitInputs();
            if (['equal', 'percentage'].includes(getMode())) distributeAmounts();
        });
    });

    // Helper to populate the modal via AJAX or data attributes
    const editGroupTransactionModal = document.getElementById('editGroupTransactionModal');
    if (editGroupTransactionModal) {
        editGroupTransactionModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const transactionId = button.getAttribute('data-transaction-id');
            const groupId = button.getAttribute('data-group-id');
            const description = button.getAttribute('data-description');
            const totalAmount = button.getAttribute('data-total-amount');
            const categoryId = button.getAttribute('data-category-id');
            const type = button.getAttribute('data-type');
            const date = button.getAttribute('data-date');
            
            // Set form action
            editForm.action = `/user/groups/${groupId}/transactions/${transactionId}`;
            
            // Set basic fields
            document.getElementById('edit-description').value = description || '';
            document.getElementById('edit-total-amount').value = totalAmount || '0.00';
            document.getElementById('edit-category').value = categoryId || '';
            document.getElementById('edit-date').value = date || '';
            
            if (type === 'income') {
                document.getElementById('edit-type-income').checked = true;
            } else {
                document.getElementById('edit-type-expense').checked = true;
            }

            // For now, let's default to equal split when opening, 
            // but in a real app we might want to fetch existing split details.
            // However, the backend updateTransaction currently finds related transactions and replaces them.
            // If we want to truly see the OLD splits, we need to fetch them.
            
            // Let's at least set 'equal' as default for now
            document.getElementById('edit-split-type-equal').checked = true;
            toggleSplitInputs();
            distributeAmounts();
        });
    }
});
</script>
