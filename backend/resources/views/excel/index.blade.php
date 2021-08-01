@extends('layouts.app')

@section('content')
    <section>
        <h1>Laravel/Excel Sample app</h1>
        <div>
            <a href="{{route('users.excel.download')}}">Export user models</a>
        </div>
        <div>
            <a href="{{route('users.excel.queue')}}">Add a job of export user models in queue</a>
        </div>
    </section>
@endsection
