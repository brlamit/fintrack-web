<!-- Add Group Transaction Modal -->
<div class="modal fade" id="addGroupTransactionModal" tabindex="-1" aria-labelledby="addGroupTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(60px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 bg-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-plus-circle text-primary" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold" id="addGroupTransactionModalLabel">Add Group Transaction</h5>
                        <p class="text-muted small mb-0">Record a new shared transaction</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('groups.split', $group) }}" method="POST" enctype="multipart/form-data" id="add-expense-form" class="row g-4">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Total Amount</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-dollar-sign text-muted"></i>
                            </span>
                            <input type="number" step="0.01" name="amount" class="form-control border-start-0 ps-0"
                                   style="border-radius: 0 8px 8px 0;" id="total-amount" placeholder="0.00">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Transaction Type</label>
                        <div class="btn-group w-100" role="group" aria-label="Transaction type">
                            <input type="radio" class="btn-check" name="type" id="type-expense" value="expense" autocomplete="off" checked>
                            <label class="btn btn-outline-danger rounded-start-pill" for="type-expense">
                                <i class="fas fa-minus-circle me-1"></i>Expense
                            </label>
                            <input type="radio" class="btn-check" name="type" id="type-income" value="income" autocomplete="off">
                            <label class="btn btn-outline-success rounded-end-pill" for="type-income">
                                <i class="fas fa-plus-circle me-1"></i>Income
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Split Method</label>
                        <div class="btn-group w-100 flex-wrap" role="group" aria-label="Split type">
                            <input type="radio" class="btn-check" name="split_type" id="split-type-equal" value="equal" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary rounded-pill me-1 mb-1" for="split-type-equal">
                                <i class="fas fa-equals me-1"></i>Equal
                            </label>
                            <input type="radio" class="btn-check" name="split_type" id="split-type-custom" value="custom" autocomplete="off">
                            <label class="btn btn-outline-secondary rounded-pill me-1 mb-1" for="split-type-custom">
                                <i class="fas fa-sliders-h me-1"></i>Custom
                            </label>
                            <input type="radio" class="btn-check" name="split_type" id="split-type-percentage" value="percentage" autocomplete="off">
                            <label class="btn btn-outline-secondary rounded-pill mb-1" for="split-type-percentage">
                                <i class="fas fa-percentage me-1"></i>Percent
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">Description (optional)</label>
                        <input type="text" name="description" class="form-control"
                               style="border-radius: 8px;" placeholder="E.g. Grocery run or Rent contribution">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Category (optional)</label>
                        <select name="category_id" class="form-control" style="border-radius: 8px;">
                            <option value="">Select a category (optional)</option>
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
                        <label class="form-label fw-semibold text-dark">Receipt (optional)</label>
                        <input type="file" name="receipt" accept="image/*" class="form-control">
                    </div>

                    <div class="col-12">
                        <hr class="my-2">
                        <h6 class="text-muted text-uppercase small mb-3">Per-member split</h6>
                    </div>

                    @foreach($group->members as $idx => $member)
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <strong>{{ $member->user->name }}</strong>
                                    <span class="text-muted small ms-2">{{ ucfirst($member->role) }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <input type="hidden" name="splits[{{ $idx }}][user_id]" value="{{ $member->user->id }}">
                                    <div class="split-input-amount">
                                        <input type="number" step="0.01" name="splits[{{ $idx }}][amount]" class="form-control split-amount" style="width: 130px;" placeholder="0.00">
                                    </div>
                                    <div class="split-input-percent d-none">
                                        <div class="input-group" style="width: 130px;">
                                            <input type="number" step="0.01" name="splits[{{ $idx }}][percent]" class="form-control split-percent" placeholder="0.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="auto-distribute">Auto distribute</button>
                    <button type="submit" form="add-expense-form" class="btn btn-primary rounded-pill px-4 shadow-sm">Save Transaction</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('add-expense-form');
    if (!form) return;

    const totalInput = document.getElementById('total-amount');
    const splitRadios = form.querySelectorAll('input[name="split_type"]');
    const autoDistributeButton = document.getElementById('auto-distribute');
    const amountContainers = form.querySelectorAll('.split-input-amount');
    const percentContainers = form.querySelectorAll('.split-input-percent');
    const amountInputs = form.querySelectorAll('.split-amount');
    const percentInputs = form.querySelectorAll('.split-percent');

    const getMode = () => form.querySelector('input[name="split_type"]:checked')?.value ?? 'equal';

    const toggleSplitInputs = () => {
        const mode = getMode();
        const usePercent = mode === 'percentage';

        percentContainers.forEach(container => container.classList.toggle('d-none', !usePercent));
        amountContainers.forEach(container => container.classList.toggle('d-none', usePercent));

        if (!usePercent) {
            percentInputs.forEach(input => (input.value = ''));
        }
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

            amountInputs.forEach(input => (input.value = '0.00'));
            return;
        }

        const total = parseFloat(totalInput?.value || '0');
        const count = amountInputs.length;
        if (!count) return;

        if (!total || total <= 0) {
            amountInputs.forEach(input => (input.value = '0.00'));
            return;
        }

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

    autoDistributeButton?.addEventListener('click', () => {
        distributeAmounts();
    });

    totalInput?.addEventListener('input', () => {
        if (getMode() === 'equal') {
            distributeAmounts();
        }
    });

    splitRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            toggleSplitInputs();
            if (['equal', 'percentage'].includes(getMode())) {
                distributeAmounts();
            }
        });
    });

    toggleSplitInputs();
    distributeAmounts();
});
</script>
