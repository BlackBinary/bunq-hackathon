@extends('layouts.app')

@section('content')
    <get-receipt :hash=`{{ $hash }}`></get-receipt>
@endsection
