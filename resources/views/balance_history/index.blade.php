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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Balance History</div>

                <div class="card-body">
                    <form action="{{ route('balance_history.index') }}" method="GET">
                        <div class="form-group">
                            <label for="user_id">User:</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">All Users</option>
                                <!-- Loop through users to populate options -->
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="search">Search:</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Transaction ID or Description">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                    @if (request('user_id') != '')
                        <div class="current-balance">
                            <strong>Current Balance:</strong> ${{ $balanceHistories[0]->new_balance }}
                        </div>
                    @endif
                    <!-- Display balance histories -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>User</th>
                                <th>Previous Balance</th>
                                <th>New Balance</th>
                                <!-- Add more columns as needed -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($balanceHistories as $balanceHistory)
                                <tr>
                                    <td>{{ $balanceHistory->transaction_id }}</td>
                                    <td>{{ $balanceHistory->user->name }}</td>
                                    <td>{{ $balanceHistory->previous_balance }}</td>
                                    <td>{{ $balanceHistory->new_balance }}</td>
                                    <!-- Add more columns as needed -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination links -->
                    {{ $balanceHistories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

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
