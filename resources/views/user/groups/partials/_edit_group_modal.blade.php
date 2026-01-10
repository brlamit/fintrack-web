<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="   transform: translateY(60px);">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="editGroupModalLabel">Edit Group Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.groups.update', $group->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label small text-muted text-uppercase fw-semibold">Group Name</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" value="{{ old('name', $group->name) }}" required style="border-radius: 12px;">
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label small text-muted text-uppercase fw-semibold">Group Type</label>
                        <select class="form-select form-select-lg bg-light border-0" id="type" name="type" required style="border-radius: 12px;">
                            <option value="family" {{ old('type', $group->type) == 'family' ? 'selected' : '' }}>👨‍👩‍👧‍👦 Family</option>
                            <option value="friends" {{ old('type', $group->type) == 'friends' ? 'selected' : '' }}>👥 Friends</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="budget_limit" class="form-label small text-muted text-uppercase fw-semibold">Monthly Budget Limit (Optional)</label>
                        <div class="input-group input-group-lg bg-light rounded-3 overflow-hidden" style="border-radius: 12px !important;">
                            <span class="input-group-text border-0 bg-transparent text-muted px-3">
                                <i class="fas fa-coins text-warning"></i>
                            </span>
                            <input type="number" step="0.01" class="form-control border-0 bg-transparent ps-0" id="budget_limit" name="budget_limit" value="{{ old('budget_limit', $group->budget_limit) }}" placeholder="0.00">
                        </div>
                        <div class="form-text small text-muted ps-2 mt-2">Notifications will be sent when spending exceeds this limit.</div>
                    </div>

                    <div class="mb-0">
                        <label for="description" class="form-label small text-muted text-uppercase fw-semibold">Description</label>
                        <textarea class="form-control bg-light border-0" id="description" name="description" rows="3" style="border-radius: 12px;" placeholder="What is this group for?">{{ old('description', $group->description) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
