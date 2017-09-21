@extends ('master')

@section('content')
    <section class="jumbotron text-center mt-5">
        <div class="container">
            <h1 class="jumbotron-heading">Zorggroep Sint Maarten - Roosterhulp</h1>
            <p class="lead text-muted mb-5">Importeer gemakkelijk je rooster in je telefoonagenda! Nooit meer foutief je rooster overnemen, of niet Judith? ;)</p>
            <form action="{{ action('RoosterController@upload') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group d-flex justify-content-center mb-5">
                    <input type="file" name="rooster-file" class="form-control-file">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Roosterbestand versturen</button>
                </div>
            </form>
        </div>
    </section>
@endsection