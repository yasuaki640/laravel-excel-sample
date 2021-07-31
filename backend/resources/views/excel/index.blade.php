@extends('layouts.app')

@section('content')
    <section>
        <h1>Laravel/Excel Sample app</h1>
        <a href="{{route('excel.download')}}">Export user models</a>
    </section>
@endsection
