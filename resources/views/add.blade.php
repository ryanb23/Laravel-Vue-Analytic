@extends('layouts.app')

@section('content')
<div class="container">
    {!! Form::open(array('route' => 'rule.store', 'class' => 'form')) !!}
    <add></add>
    {!! Form::close() !!}
</div>
@endsection
