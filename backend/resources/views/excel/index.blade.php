@extends('layouts.app')

@section('content')
    <section>
        <h1>Laravel/Excel Sample app</h1>
        <button onclick="handleExportButtonClick">Export user models</button>
    </section>
    <script>
        export default {
            methods: {
                handleExportButtonClick() {
                    alert('fuck')
                }
            }
        }
    </script>
@endsection
