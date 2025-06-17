@extends('modules.saasconnector::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('modules.saasconnector.name') !!}</p>
@endsection
