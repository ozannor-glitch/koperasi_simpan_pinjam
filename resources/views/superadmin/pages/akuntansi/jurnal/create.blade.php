@extends('superadmin.components.template')

@section('title')
    Buat Jurnal Umum
@endsection

@section('content')
<form action="{{ route('superadmin.jurnal.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label>Tanggal</label>
    <input type="date" name="date" class="form-control" required>
</div>

<div class="mb-3">
    <label>Keterangan</label>
    <input type="text" name="description" class="form-control" required>
</div>

<hr>

<h5>Detail Jurnal</h5>

<table class="table" id="jurnalTable">
    <thead>
        <tr>
            <th>Akun</th>
            <th>Debit</th>
            <th>Credit</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td>
                <select name="accounts[]" class="form-control" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="debit[]" class="form-control" value="0">
            </td>
            <td>
                <input type="number" name="credit[]" class="form-control" value="0">
            </td>
            <td>
                <button type="button" class="btn btn-danger removeRow">X</button>
            </td>
        </tr>

    </tbody>
</table>

<button type="button" id="addRow" class="btn btn-secondary mb-3">
    + Tambah Baris
</button>

<br>

<button class="btn btn-primary">Simpan</button>

</form>

<script>
document.getElementById('addRow').addEventListener('click', function () {

    let table = document.querySelector('#jurnalTable tbody');
    let row = table.rows[0].cloneNode(true);

    row.querySelectorAll('input').forEach(input => input.value = 0);

    table.appendChild(row);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow')) {
        let rows = document.querySelectorAll('#jurnalTable tbody tr');
        if (rows.length > 1) {
            e.target.closest('tr').remove();
        }
    }
});
</script>
@endsection
