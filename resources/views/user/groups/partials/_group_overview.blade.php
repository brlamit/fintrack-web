<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">Group Overview</h5>
    </div>
    <div class="card-body">
        @php
            $currency = fn (float $value) => '$' . number_format($value, 2);
            $currentMember = $group->members->firstWhere('user_id', auth()->id());
        @endphp
        <dl class="row mb-0">
            <dt class="col-6 text-muted">Owner</dt>
            <dd class="col-6 text-end">{{ $group->owner->name }}</dd>

            <dt class="col-6 text-muted">Owner Email</dt>
            <dd class="col-6 text-end">{{ $group->owner->email }}</dd>

            <dt class="col-6 text-muted">Your role</dt>
            <dd class="col-6 text-end">{{ ucfirst(optional($currentMember)->role ?? 'member') }}</dd>

            <dt class="col-6 text-muted">Members</dt>
            <dd class="col-6 text-end">{{ number_format($group->members->count()) }}</dd>

            <dt class="col-6 text-muted">Budget Limit</dt>
            <dd class="col-6 text-end">{{ $group->budget_limit ? $currency($group->budget_limit) : '—' }}</dd>

            <dt class="col-6 text-muted">Invite Code</dt>
            <dd class="col-6 text-end text-uppercase">{{ $group->invite_code ?? '—' }}</dd>
        </dl>

        @if($group->description)
            <hr>
            <p class="text-muted small mb-1">Description</p>
            <p class="mb-0">{{ $group->description }}</p>
        @endif
    </div>
</div>
