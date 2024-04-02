@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Transactions')
@section('content_header_title', 'Transactions & Balance')
@section('content_header_subtitle', 'Transactions')
@section('content_top_nav_left')
<li class="nav-header ">

   Transactions

</li>
@stop
{{-- Content body: main page content --}}

@section('content_body')
{{-- Setup data for datatables --}}
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@php
$heads = [
    'ID',
    'User ID',
    'Type',
    'Amount',
    'Description',
    'Proof',
    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
];

// Example buttons for actions
$btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                <i class="fa fa-lg fa-fw fa-pen"></i>
            </button>';
$btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                  <i class="fa fa-lg fa-fw fa-trash"></i>
              </button>';
$btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                   <i class="fa fa-lg fa-fw fa-eye"></i>
               </button>';

// Fetching transactions from the database

@endphp

{{-- Minimal example / fill data using the component slot --}}
<x-adminlte-modal id="modalCustom" title="Account Policy" size="lg" theme="teal"
    icon="fas fa-bell" v-centered static-backdrop scrollable>
    <form id="transactionForm" action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="type">Type:</label>
            <select name="type" id="type" class="form-control">
                <option value="topup">Top Up</option>
                <option value="transaction">Regular</option>
            </select>
        </div>
        <div class="form-group">
            <label for="user_id">User:</label>
            <select name="user_id" id="user_id" class="form-control">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="text" name="amount" id="amount" class="form-control" placeholder="Enter amount">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" placeholder="Enter description"></textarea>
        </div>
        <div class="form-group">
            <label for="proof">Proof:</label>
            <input type="file" name="proof" id="proof" class="form-control-file">
        </div>
        <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
    </form>
    <x-slot name="footerSlot">
        <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal"/>
    </x-slot>
</x-adminlte-modal>

<script>
    function submitForm() {
        // Submit the form
        document.getElementById('transactionForm').submit();
    }
</script>
@section('plugins.Datatables', true)
<div class="text-right mb-4">
<x-adminlte-button label="New Transaction" data-toggle="modal" data-target="#modalCustom" class="bg-teal"/>
</div>
@php
$heads = [
    'ID',
    'Transaction Code',
    'User Name',
    'Type',
    'Amount',
    'Description',
    ['label' => 'Proof', 'no-export' => true],
];

$config = [
    'data' => [],
    'order' => [[1, 'asc']],
    'columns' => [null, null,null, null, null, null, ['orderable' => false], 
    // ['orderable' => false]
    ],
];

foreach ($transactions as $transaction) {
    $proofLink = $transaction->proof ? '<a href="' . $transaction->proof . '" target="_blank">Proof</a>' : 'N/A';
    $actions = '<nobr>' . $btnEdit . $btnDelete . $btnDetails . '</nobr>';
    $config['data'][] = [
        $transaction->id,
        $transaction->transaction_code,
        $transaction->user->name,
        $transaction->type,
        $transaction->amount,
        $transaction->description,
        $proofLink,
        // $actions,
    ];
}

@endphp

<x-adminlte-datatable id="transaction_table" :heads="$heads" :config="$config" theme="light" striped hoverable/>

@stop

{{-- Push extra CSS --}}

@push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@endpush
