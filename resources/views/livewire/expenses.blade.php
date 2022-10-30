<div class="card">
    <div class="card-header">
        <h3 class="card-title">Expenses</h3>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <button wire:click="addRow" class="btn btn-info" type="button">Add Row</button>
        </div>

        <table class="table table-bordered table-sm table-hover">
            <thead>
                <tr>
                    <th>Detail</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $key => $row)
                    <tr>
                        <td><input wire:model="expenses.{{ $key }}.detail" name="expenses[{{ $key }}][detail]" type="text" class="form-control" required></td>
                        <td><input wire:model="expenses.{{ $key }}.amount" name="expenses[{{ $key }}][amount]" type="number" class="form-control" min="1" required></td>
                        <td><button wire:click="removeRow({{ $key }})" class="btn btn-danger" type="button" tabindex="-1">Remove</button></td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan=3>Add row to input expenses</td>
                    </tr>
                @endforelse
            </tbody>
            
            @if ($rows)
                <tfoot>
                    <tr>
                        <td class="text-center" colspan=3><strong>Total Expenses:</strong> {{ $totalExpenses }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>