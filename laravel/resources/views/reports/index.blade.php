@extends('layouts.app')

@section('title', 'Отчеты для ФСЗН - SalaryCalc BY')

@section('content')
    @include('components.stats-cards', ['stats' => $stats])
@endsection
