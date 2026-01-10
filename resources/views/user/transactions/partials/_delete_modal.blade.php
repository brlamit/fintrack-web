<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="  transform: translateY(40px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 bg-danger bg-opacity-10 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-danger">Delete Transaction</h5>
                        <p class="text-muted small mb-0">This action cannot be undone</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle text-warning me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Warning:</strong> Deleting this transaction will remove it permanently.
                        <div id="groupDeleteWarning" class="mt-2 d-none">
                            Since this is a group transaction, it will be removed for <strong>all members</strong>.
                        </div>
                    </div>
                </div>
                
                <div class="card bg-light border-0 mb-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="text-muted small mb-1">Description</p>
                                <p class="fw-bold mb-0 text-dark" id="deleteDescription">-</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted small mb-1">Amount</p>
                                <p class="fw-bold mb-0 text-danger" id="deleteAmount">-</p>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small mb-1">Category</p>
                                <p class="mb-0 text-dark" id="deleteCategory">-</p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="text-muted small mb-1">Date</p>
                                <p class="mb-0 text-dark" id="deleteDate">-</p>
                            </div>
                            <div class="col-12 border-top pt-2">
                                <p class="text-muted small mb-1">Type</p>
                                <p class="mb-0 text-dark text-uppercase fw-semibold" id="deleteType">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTransactionForm" method="POST" class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 w-100 shadow-sm">
                        <i class="fas fa-trash-alt me-2"></i>Delete Transaction
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
