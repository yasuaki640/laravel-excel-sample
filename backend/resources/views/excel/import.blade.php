@extends('layouts.app')

@section('content')
    <section>
        <h1>Laravel/Excel Sample app</h1>
        <div>
            <p>Import</p>
            <form
                action="{{ route('users.excel.import.upload') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div>
                    <input type="file" id="users" name="users">
                </div>
                <div>
                    <input type="submit" value="Import">
                </div>
            </form>
        </div>
        <div>
            <p>Import by queue</p>
            <form
                action="{{ route('users.excel.import.queue') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div>
                    <input type="file" id="users" name="users">
                </div>
                <div>
                    <input type="submit" value="Import by queue">
                </div>
            </form>
        </div>
    </section>
@endsection
