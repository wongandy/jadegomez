<div class="card">
    <div class="card-header">
        <h3 class="card-title">Checks</h3>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <button wire:click="addRow" class="btn btn-info" type="button">Add Row</button>
        </div>

        <table class="table table-bordered table-sm table-hover">
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>Check Number</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($rows as $key => $row)
                    <tr>
                        <td><input wire:model="checks.{{ $key }}.bankName" name="checks[{{ $key }}][bank_name]" type="text" class="form-control" required></td>
                        <td><input wire:model="checks.{{ $key }}.checkNumber" name="checks[{{ $key }}][check_number]" type="text" class="form-control" required></td>
                        <td><input wire:model="checks.{{ $key }}.amount" name="checks[{{ $key }}][amount]" type="number" class="form-control" min="1" required></td>
                        <td><button wire:click="removeRow({{ $key }})" class="btn btn-danger" type="button" tabindex="-1">Remove</button></td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan=4>Add row to input checks</td>
                    </tr>
                @endforelse
            </tbody>

            @if ($rows)
                <tfoot>
                    <tr>
                        <td class="text-center" colspan=4><strong>Total Checks:</strong> {{ $totalChecks }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>