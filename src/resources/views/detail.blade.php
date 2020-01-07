{{ '@' }}extends('layouts.app')

{{ '@' }}section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <h3>{{ $model }}</h3>
            </div>
            <div class="card-body">
                <form>

@foreach($columns as $column)
                    <div class="form-group row">
                        <label for="{{ $column }}" class="col-sm-2 col-form-label">{{ ucfirst($column) }}</label>
                        <div class="col-sm-10">
                            <input type="text" id="{{ $column }}" value="{{ blade('$result->'.$column) }}" class="form form-control">
                        </div>
                    </div>

@endforeach
                </form>
            </div>
        </div>
    </div>
{{ '@' }}endsection
