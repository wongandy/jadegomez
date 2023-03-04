<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cash</h3>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-sm table-hover">
            <thead>
                <tr>
                    <th>Denomination</th>
                    <th>Pieces</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($cashDenominations as $cashDenomination)
                    <tr>
                        <td>{{ $cashDenomination->number }}</td>
                        <td>
                            <input wire:model="cashes.{{ $cashDenomination->id }}.pieces" name="cashes[{{ $cashDenomination->id }}][pieces]" type="number" class="form-control" min="1">
                            <input name="cashes[{{ $cashDenomination->id }}][cash_denomination_id]" type="hidden" value="{{ $cashDenomination->id }}">
                        </td>
                        <td>{{ isset($cashes[$cashDenomination->id]['amount']) ? $cashes[$cashDenomination->id]['amount'] : '' }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td class="text-center" colspan=3><strong>Total Cash:</strong> {{ $totalCashes }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
