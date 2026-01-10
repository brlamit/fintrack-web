<!-- Delete Group Modal -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(60px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 bg-danger bg-opacity-10 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 1.2rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-danger" id="deleteGroupModalLabel">Delete Group</h5>
                        <p class="text-muted small mb-0">Extreme caution required</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <i class="fas fa-users-slash text-muted" style="font-size: 4rem; opacity: 0.2;"></i>
                </div>
                <h5 class="fw-bold mb-3">Are you absolutely sure?</h5>
                <p class="text-muted px-3">
                    You are about to delete <strong>{{ $group->name }}</strong>. This action will permanently remove:
                </p>
                <div class="bg-light rounded-3 p-3 mb-4 text-start mx-auto" style="max-width: 320px;">
                    <ul class="list-unstyled mb-0 small text-secondary">
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> All shared transactions</li>
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Member associations & history</li>
                        <li><i class="fas fa-check text-danger me-2"></i> Group budget & settings</li>
                    </ul>
                </div>
                <p class="text-danger small fw-bold">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('user.groups.destroy', $group->id) }}" method="POST" class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 w-100 shadow-sm">
                        <i class="fas fa-trash-alt me-2"></i>Delete Group
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
