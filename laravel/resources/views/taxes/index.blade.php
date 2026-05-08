@extends('layouts.app')

@section('title', 'Налоги и платежи - SalaryCalc BY')

@section('content')
    @include('components.stats-cards', ['stats' => $stats])
@endsection
