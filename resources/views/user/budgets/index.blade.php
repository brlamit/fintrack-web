@extends('layouts.user')

@section('title', 'My Budgets')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Budgets</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBudgetModal" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); border: none;">
            <i class="fas fa-plus-circle"></i> Create Budget
        </button>
    </div>

    <!-- Create Budget Modal -->
    <div class="modal fade" id="createBudgetModal" tabindex="-1" aria-labelledby="createBudgetLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: 80px;">
            <div class="modal-content">
                <form method="POST" action="{{ route('user.budgets.store') }}">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%);">
                        <h5 class="modal-title text-white" id="createBudgetLabel">Create Budget</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input name="name" class="form-control" value="{{ old('name') }}" required />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input name="amount" type="number" step="0.01" class="form-control" value="{{ old('amount') }}" required />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Period</label>
                                <select name="period" id="createPeriod" class="form-select" required>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">General</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input name="start_date" id="createStartDate" type="date" class="form-control" value="{{ old('start_date', now()->startOfMonth()->toDateString()) }}" required />
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input name="end_date" id="createEndDate" type="date" class="form-control" value="{{ old('end_date', now()->endOfMonth()->toDateString()) }}" required />
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alert thresholds (comma-separated percentages)</label>
                                <input name="alert_thresholds" class="form-control" value="{{ old('alert_thresholds', '50,75,90') }}" placeholder="e.g. 50,75,90" />
                                <small class="text-muted">Values will be parsed as percentages.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); border: none;">Create Budget</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($budgets as $budget)
            @php
                $spent = (float) ($budget->spent ?? 0);
                $limit = (float) ($budget->limit_amount ?? 0);
                $percent = $limit > 0 ? min(($spent / $limit) * 100, 100) : 0;
                $remaining = $limit - $spent;
                if ($remaining < 0) { $remaining = 0; }
                $barClass = 'bg-success';
                if ($percent >= 90) { $barClass = 'bg-danger'; }
                elseif ($percent >= 60) { $barClass = 'bg-warning'; }
            @endphp

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $budget->name }}</h6>
                                <div class="text-muted small">{{ $budget->category->name ?? 'General' }}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">{{ ucfirst($budget->period) }}</span>
                                @if(!$budget->is_active)
                                    <span class="badge bg-secondary ms-1">Paused</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 text-muted small">
                                <div>Limit</div>
                                <div class="fw-bold">${{ number_format($limit, 2) }}</div>
                            </div>
                            <div class="me-3 text-muted small">
                                <div>Spent</div>
                                <div class="fw-bold text-{{ $percent >= 90 ? 'danger' : ($percent >= 60 ? 'warning' : 'success') }}">${{ number_format($spent, 2) }}</div>
                            </div>
                            <div class="text-muted small ms-auto text-end">
                                <div>Remaining</div>
                                <div class="fw-bold">${{ number_format($remaining, 2) }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="progress" style="height:18px;">
                                <div class="progress-bar {{ $barClass }}" role="progressbar" style="width: {{ round($percent, 2) }}%;" aria-valuenow="{{ round($percent, 2) }}" aria-valuemin="0" aria-valuemax="100">
                                    <small class="ps-2">{{ round($percent) }}%</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 small text-muted">
                            <strong class="me-2">Period:</strong> {{ ucfirst($budget->period) }}
                            <span class="mx-2">•</span>
                            <strong class="me-2">From:</strong> {{ Illuminate\Support\Carbon::parse($budget->start_date)->format('M j, Y') }}
                            <span class="mx-2">•</span>
                            <strong class="me-2">To:</strong> {{ Illuminate\Support\Carbon::parse($budget->end_date)->format('M j, Y') }}
                        </div>

                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                <strong>Alerts:</strong>
                                @if(is_array($budget->alert_thresholds) && count($budget->alert_thresholds))
                                    @foreach($budget->alert_thresholds as $t)
                                        <span class="badge bg-light text-dark border me-1">{{ (int)$t }}%</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editBudgetModal-{{ $budget->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBudgetModal" data-budget-id="{{ $budget->id }}" data-budget-name="{{ $budget->name }}" data-budget-amount="{{ $budget->amount }}" data-budget-period="{{ $budget->period }}" data-budget-category="{{ $budget->category->name ?? 'General' }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Budget Modal (unchanged form content) -->
            <div class="modal fade" id="editBudgetModal-{{ $budget->id }}" tabindex="-1" aria-labelledby="editBudgetLabel-{{ $budget->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" style="margin-top: 80px;">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('user.budgets.update', $budget) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%);">
                                <h5 class="modal-title text-white" id="editBudgetLabel-{{ $budget->id }}">Edit Budget</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input name="name" class="form-control" value="{{ old('name', $budget->name) }}" required />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Amount</label>
                                        <input name="amount" type="number" step="0.01" class="form-control" value="{{ old('amount', $budget->amount) }}" required />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Period</label>
                                        <select name="period" class="form-select editPeriod" data-budget-id="{{ $budget->id }}" required>
                                            <option value="weekly" {{ $budget->period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ $budget->period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="quarterly" {{ $budget->period === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="yearly" {{ $budget->period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-select">
                                            <option value="">General</option>
                                            @foreach($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}" {{ $budget->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Start Date</label>
                                        <input name="start_date" type="date" class="form-control editStartDate" data-budget-id="{{ $budget->id }}" value="{{ old('start_date', optional($budget->start_date)->toDateString() ?? $budget->start_date) }}" required />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input name="end_date" type="date" class="form-control editEndDate" data-budget-id="{{ $budget->id }}" value="{{ old('end_date', optional($budget->end_date)->toDateString() ?? $budget->end_date) }}" required />
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Alert thresholds (comma-separated percentages)</label>
                                        <input name="alert_thresholds" class="form-control" value="{{ old('alert_thresholds', is_array($budget->alert_thresholds) ? implode(',', $budget->alert_thresholds) : $budget->alert_thresholds) }}" placeholder="e.g. 50,75,90" />
                                        <small class="text-muted">Values will be parsed as percentages.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #14b8a6 0%, #0ea5e9 100%); border: none;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card text-center border-0 shadow-sm p-5">
                    <h4 class="mb-3">No budgets yet</h4>
                    <p class="text-muted mb-4">Create budgets to track your spending and get alerts when you approach your limits.</p>
                    <button class="btn btn-lg btn-primary" data-bs-toggle="modal" data-bs-target="#createBudgetModal">
                        <i class="fas fa-plus-circle me-2"></i>Create your first budget
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($budgets->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $budgets->links() }}
        </div>
    @endif

    <!-- Delete Budget Modal -->
    <div class="modal fade" id="deleteBudgetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="top: 60px;"> ">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background-color: #ffffff; color: #111827;">
                <div class="modal-header border-0 bg-danger bg-opacity-10">
                    <h5 class="modal-title text-danger fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Delete Budget
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="color: #6b7280 !important;">Review the details below before deleting:</p>
                    
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="color: #6b7280 !important;">Name</small>
                                    <strong id="deleteBudgetName" style="color: #111827;">-</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block" style="color: #6b7280 !important;">Amount</small>
                                    <strong id="deleteBudgetAmount" class="text-danger">-</strong>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="color: #6b7280 !important;">Category</small>
                                    <strong id="deleteBudgetCategory" style="color: #111827;">-</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block" style="color: #6b7280 !important;">Period</small>
                                    <strong id="deleteBudgetPeriod" style="color: #111827;">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger border-0" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteBudgetForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Budget
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
</style>

<script>
    // Delete budget modal handler
    const deleteBudgetModal = document.getElementById('deleteBudgetModal');
    if (deleteBudgetModal) {
        deleteBudgetModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const budgetId = button.getAttribute('data-budget-id');
            const budgetName = button.getAttribute('data-budget-name');
            const budgetAmount = button.getAttribute('data-budget-amount');
            const budgetPeriod = button.getAttribute('data-budget-period');
            const budgetCategory = button.getAttribute('data-budget-category');

            document.getElementById('deleteBudgetName').textContent = budgetName;
            document.getElementById('deleteBudgetAmount').textContent = '$' + parseFloat(budgetAmount).toFixed(2);
            document.getElementById('deleteBudgetCategory').textContent = budgetCategory;
            document.getElementById('deleteBudgetPeriod').textContent = budgetPeriod.charAt(0).toUpperCase() + budgetPeriod.slice(1);

            const deleteForm = document.getElementById('deleteBudgetForm');
            deleteForm.action = `/budgets/${budgetId}`;
        });
    }

    // Budget period date calculation functions
    function calculateDates(period) {
        const today = new Date();
        let startDate, endDate;

        switch(period) {
            case 'weekly':
                // Current week (Monday to Sunday)
                const day = today.getDay();
                const diff = today.getDate() - day + (day === 0 ? -6 : 1); // Adjust to Monday
                startDate = new Date(today.setDate(diff));
                endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + 6); // Add 6 days for Sunday
                break;
            case 'monthly':
                // Current month
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'quarterly':
                // Current quarter
                const quarter = Math.floor(today.getMonth() / 3);
                startDate = new Date(today.getFullYear(), quarter * 3, 1);
                endDate = new Date(today.getFullYear(), (quarter + 1) * 3, 0);
                break;
            case 'yearly':
                // Current year
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
                break;
            default:
                startDate = today;
                endDate = today;
        }

        // Convert to date string format (YYYY-MM-DD)
        return {
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0]
        };
    }

    // Handle create budget period change
    document.addEventListener('DOMContentLoaded', function() {
        const createPeriodSelect = document.getElementById('createPeriod');
        const createStartDate = document.getElementById('createStartDate');
        const createEndDate = document.getElementById('createEndDate');

        if (createPeriodSelect) {
            createPeriodSelect.addEventListener('change', function() {
                const dates = calculateDates(this.value);
                createStartDate.value = dates.start;
                createEndDate.value = dates.end;
            });
        }

        // Handle edit budget period changes
        const editPeriodSelects = document.querySelectorAll('.editPeriod');
        editPeriodSelects.forEach(select => {
            select.addEventListener('change', function() {
                const budgetId = this.getAttribute('data-budget-id');
                const startInput = document.querySelector(`.editStartDate[data-budget-id="${budgetId}"]`);
                const endInput = document.querySelector(`.editEndDate[data-budget-id="${budgetId}"]`);
                
                const dates = calculateDates(this.value);
                startInput.value = dates.start;
                endInput.value = dates.end;
            });
        });
    });
</script>

@endsection
