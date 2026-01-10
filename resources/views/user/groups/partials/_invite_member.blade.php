@if($isAdmin)
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">Invite Member</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('groups.invite', $group) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-12">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success w-100">Send Invite</button>
            </div>
        </form>
    </div>
</div>
@endif

