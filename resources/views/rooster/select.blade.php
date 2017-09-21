@extends('master')

@section('content')
    <section class="jumbotron text-center mt-5">
        <div class="container">
            <h1 class="jumbotron-heading">Hoe heet je?</h1>
            <p class="lead text-muted mb-5">Selecteer hieronder je naam, dan doen wij de rest!</p>
            <form action="{{ action('RoosterController@generate') }}" method="post">
                {{ csrf_field() }}
                <input name="rooster-data" type="hidden" value="{{ serialize($roosters) }}">
                <div class="form-group d-flex justify-content-center mb-5">
                    <select name="rooster-index" class="custom-select">
                        @foreach ($roosters as $rooster)
                            <option value="{{ $loop->index }}">{{ $rooster->naam }} ({{ $rooster->personeelsNummer }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Genereer agendabestand</button>
                </div>
            </form>
        </div>
    </section>
@endsection