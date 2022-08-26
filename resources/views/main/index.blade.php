@extends('layout')

@section('content')

    {{--<form action="  {{ route( 'CheckSave' , ['accountId' => $accountId] ) }} " method="post">
    @csrf <!-- {{ csrf_field() }} -->
        <button class="btn btn-outline-dark textHover"> check </button>

    </form>--}}

    @php
    $max = random_int(1, 1000);
    for ( $i = 1; $i<=$max; $i++ ){
    echo $i. " ";
    }

    @endphp
@endsection

