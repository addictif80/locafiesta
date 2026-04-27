@extends('layouts.admin')
@section('title', 'Modifier la facture')
@section('content')
@php redirect(route('admin.factures.show', $invoice))->send(); @endphp
@endsection
