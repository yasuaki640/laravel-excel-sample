@extends('layouts.app')

@section('content')
    <section>
        <h1>Laravel/Excel Sample app</h1>
        <div>
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
                    <input type="submit" value="Upload">
                </div>
            </form>
        </div>
    </section>
@endsection
