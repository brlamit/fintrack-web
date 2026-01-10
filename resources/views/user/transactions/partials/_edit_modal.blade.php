<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(45px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background-color: #ffffff; color: #111827;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%);">
                <h5 class="modal-title text-white fw-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-edit me-2"></i>Edit Transaction
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editTransactionForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Description Field -->
                    <div class="mb-3">
                        <label for="editDescription" class="form-label fw-semibold" style="color: #111827;">Description</label>
                        <input type="text" class="form-control" id="editDescription" name="description" required style="background-color: #ffffff; color: #111827; border-color: #d1d5db; border-radius: 8px;">
                    </div>

                    <!-- Type and Amount Row -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #111827;">Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="editExpense" value="expense">
                                <label class="btn btn-outline-danger rounded-start-3" for="editExpense" style="border-radius: 12px 0 0 12px !important;">
                                    <i class="fas fa-minus-circle me-2"></i>Expense
                                </label>
                                <input type="radio" class="btn-check" name="type" id="editIncome" value="income">
                                <label class="btn btn-outline-success rounded-end-3" for="editIncome" style="border-radius: 0 12px 12px 0 !important;">
                                    <i class="fas fa-plus-circle me-2"></i>Income
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="editAmount" class="form-label fw-semibold" style="color: #111827;">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-dollar-sign text-muted"></i>
                                </span>
                                <input type="number" class="form-control" id="editAmount" name="amount" step="0.01" min="0" required style="background-color: #ffffff; color: #111827; border-color: #d1d5db; border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                    </div>

                    <!-- Category and Date Row -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="editCategory" class="form-label fw-semibold" style="color: #111827;">Category</label>
                            <select class="form-select" id="editCategory" name="category_id" required style="background-color: #ffffff; color: #111827; border-color: #d1d5db; border-radius: 8px;">
                                <option value="">Select a category</option>
                                @php
                                    $groupedCategories = $categories->groupBy('type');
                                @endphp
                                @foreach($groupedCategories as $type => $typeCategories)
                                    <optgroup label="{{ ucfirst($type ?? 'Uncategorized') }}">
                                        @foreach($typeCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="editDate" class="form-label fw-semibold" style="color: #111827;">Date</label>
                            <input type="date" class="form-control" id="editDate" name="transaction_date" max="{{ now()->format('Y-m-d') }}" required style="background-color: #ffffff; color: #111827; border-color: #d1d5db; border-radius: 8px;">
                        </div>
                    </div>

                    <!-- Receipt Field -->
                    <div class="mb-3">
                        <label for="editReceipt" class="form-label fw-semibold" style="color: #111827;">Receipt (optional)</label>
                        <input type="file" class="form-control" id="editReceipt" name="receipt" accept="image/*" style="background-color: #ffffff; color: #111827; border-color: #d1d5db; border-radius: 8px;">
                        <div id="editReceiptPreview" class="mt-2 text-center d-none">
                            <p class="text-muted small mb-1">Current Receipt:</p>
                            <img src="" id="editReceiptImage" alt="Current Receipt" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editTransactionForm" class="btn btn-primary" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); border: none;">
                    <i class="fas fa-save me-2"></i>Update Transaction
                </button>
            </div>
        </div>
    </div>
</div>
