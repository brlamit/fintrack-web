@extends('layouts.user')

@section('title', $group->name)

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

    @php
        $currency = fn (float $value) => '$' . number_format($value, 2);
        $lastActivity = $transactionMetrics['last_activity'] ?? null;
        if ($lastActivity && ! $lastActivity instanceof \Carbon\CarbonInterface) {
            $lastActivity = \Illuminate\Support\Carbon::parse($lastActivity);
        }
        $currentMember = $group->members->firstWhere('user_id', auth()->id());
        $isAdmin = $currentMember && $currentMember->role === 'admin';
        $isOwner = $group->owner_id === auth()->id();
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="mb-0">{{ $group->name }}</h2>
                <span class="badge bg-primary-subtle text-primary text-uppercase">{{ ucfirst($group->type) }}</span>
            </div>
            <p class="text-muted mb-0">
                You joined {{ optional(optional($currentMember)->joined_at)->format('M d, Y') ?? '—' }} · Owner {{ $group->owner->name }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addGroupTransactionModal">
                <i class="fas fa-plus-circle me-2"></i>Add Transaction
            </button>
            @if($isAdmin || $isOwner)
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editGroupModal">
                    <i class="fas fa-edit me-2"></i>Edit Group
                </button>
                @if($isOwner)
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
                    <i class="fas fa-trash-alt me-2"></i>Delete Group
                </button>
                @endif
            @endif
            <a href="{{ route('user.groups') }}" class="btn  rounded-pill">
                <i class="fas fa-arrow-left me-2"></i>Back to Groups
            </a>
        </div>
    </div>

    <!-- Modern Metrics Cards -->
    <div class="row g-4 mb-4">
        <!-- Financial Overview -->
        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-arrow-up text-success" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Total Income</p>
                            <h4 class="text-success fw-bold mb-0">{{ $currency($groupTotals['income']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-arrow-down text-danger" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Total Expense</p>
                            <h4 class="text-danger fw-bold mb-0">{{ $currency($groupTotals['expense']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-balance-scale text-primary" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Net Flow</p>
                            @php $net = $groupTotals['net']; @endphp
                            <h4 class="fw-bold mb-0 {{ $net >= 0 ? 'text-success' : 'text-danger' }}">{{ $currency($net) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-receipt text-info" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Total Transactions</p>
                            <h4 class="fw-bold mb-0">{{ number_format($transactionMetrics['count']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-line text-warning" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Average Amount</p>
                            <h4 class="fw-bold mb-0">{{ $currency($transactionMetrics['average']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-clock text-secondary" style="font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted text-uppercase small mb-1">Last Activity</p>
                            <h6 class="fw-bold mb-1">{{ $lastActivity ? $lastActivity->format('M d, Y') : 'No activity yet' }}</h6>
                            @if($lastActivity)
                                <span class="text-muted small">{{ $lastActivity->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('user.groups.partials._member_list')

            @include('user.groups.partials._recent_transactions')
        </div>

        <div class="col-lg-4">
            @include('user.groups.partials._group_overview')

            @include('user.groups.partials._invite_member')
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

@include('user.groups.partials._add_transaction_modal')
@include('user.groups.partials._edit_transaction_modal')
@if($isAdmin || $isOwner)
    @include('user.groups.partials._edit_group_modal')
    @include('user.groups.partials._delete_group_modal')
@endif

@push('styles')
<style>
.hover-shadow-lg {
    transition: all 0.3s ease;
}

.hover-shadow-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.input-group-text {
    border-radius: 8px 0 0 8px !important;
}

.form-control, .form-select {
    border-radius: 0 8px 8px 0 !important;
}

.modal-content {
    border: none !important;
}

.btn-group .btn {
    border-radius: 8px !important;
}

.btn-outline-secondary {
    border-color: #dee2e6 !important;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa !important;
    border-color: #adb5bd !important;
}
</style>
@endpush
@endsection