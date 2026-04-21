@extends('superadmin.components.template')

@section('title')
    Jurnal Umum
@endsection

@section('content')

<h4 class="mb-3">Jurnal Umum</h4>
<a href="{{ route('superadmin.jurnal.create') }}"
   class="btn btn-success mb-3">+ Tambah Jurnal</a>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th width="120">Tanggal</th>
        <th>Akun</th>
        <th width="150" class="text-end">Debit</th>
        <th width="150" class="text-end">Kredit</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>

@forelse($journals as $journal)

{{-- ✅ KETERANGAN (HANYA SEKALI) --}}
<tr>
    <td colspan="5">
        <strong>{{ $journal->description }}</strong>
    </td>
</tr>

{{-- ✅ DETAIL --}}
@foreach($journal->items as $index => $item)
<tr>
    <td>
        @if($index == 0)
            {{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}
        @endif
    </td>

    <td style="padding-left: {{ $item->credit > 0 ? '40px' : '0px' }}">
        {{ $item->account->name ?? '-' }}
    </td>

    <td class="text-end">
        {{ $item->debit > 0 ? number_format($item->debit,0,',','.') : '' }}
    </td>

    <td class="text-end">
        {{ $item->credit > 0 ? number_format($item->credit,0,',','.') : '' }}
    </td>

   @if($index == 0)
<td rowspan="{{ count($journal->items) }}" style="vertical-align: middle;">

    <div style="display:flex; justify-content:center; align-items:center; gap:6px; height:100%;">

        <a href="{{ route('superadmin.jurnal.edit', $journal->id) }}"
           class="btn btn-warning btn-sm mb-1">
           Edit
        </a>

        <form action="{{ route('superadmin.jurnal.destroy', $journal->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">
                Hapus
            </button>
        </form>

    </div>

</td>
@endif
</tr>
@endforeach

@empty
<tr>
    <td colspan="5" class="text-center text-muted">
        Belum ada data jurnal
    </td>
</tr>
@endforelse

    </tbody>
</table>

@endsection
