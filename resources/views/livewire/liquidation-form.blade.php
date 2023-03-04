<form action="{{ route('liquidation.store') }}" method="POST">
    @csrf
    <div class="card-body">
        @livewire('checks')

        @livewire('expenses')

        @livewire('cash-denominations')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Summary</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12 order-2 order-md-1">
                        <div class="row">
                            <div class="col-lg-3 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Total Checks</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $totalChecks ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Total Expenses</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $totalExpenses ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Total Cash</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $totalCashes ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-4">
                                <div class="info-box bg-light">
                                    <div class="info-box-content">
                                        <span class="info-box-text text-center text-muted">Total Sales</span>
                                        <span class="info-box-number text-center text-muted mb-0">{{ $totalSales ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notes</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <textarea name="notes" id="" style="width: 100%" rows="10" placeholder=" Type any notes here..."></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <button type="submit" class="btn btn-success" {{ $totalSales ? '' : 'disabled' }} onClick="return confirm('Are you sure to create liquidation form?');">Create Liquidation Form</button>
    </div>
</form>
