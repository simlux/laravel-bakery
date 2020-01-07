{{ '@' }}extends('layouts.app')

{{ '@' }}section('content')
<div class="{{ $containerClass }}">

    <div class="card">
        <div class="card-header">
            <{{ $titleTag }}>{{ $model }}</{{ $titleTag }}>
        </div>
        <div class="card-body">
            <table class="{{ $tableClass }}">
                <thead>
                <tr>
@foreach($columns as $column)
                    <th>{{ $column }}</th>
@endforeach
                    <th class="text-center" width="1">Actions</th>
                </tr>
                </thead>
                <tbody>
                {{ '@' }}foreach($results as $result)
                <tr>
                    {!! $columnCells !!}
                    <td class="text-center">
                        <a href="{!! $edit_route !!}" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="left" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    </td>
                </tr>
                {{ '@' }}endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ '@' }}endsection
