@extends('layouts.app')

@section('content')
<div class="container">

    {!! Form::open(array('route' => array('rule.update',$id) , 'method' => 'PUT', 'class' => 'form')) !!}
    <edit :experiment-data="'{{ json_encode($experiment) }}'"></edit>
    {!! Form::close() !!}
</div>
@endsection
