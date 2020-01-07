namespace {{ $model->getNamespace() }};

{{ $uses }}

/**
 * Class {{ $model->getName() }}
 *
 * {{ '@' }}package {{ $model->getNamespace() }}
{{ $model->getPropertiesAsString() }}
 */
class {{ $model->getName() }} extends {{ $model->getExtends(false) }}
{
    /**
     * {{ '@' }}var string
     */
    protected $table = '{{ $model->getTable() }}';

    /**
     * {{ '@' }}var bool
     */
    public $timestamps = false;

    /**
     * {{ '@' }}var array
     */
    protected $dates = [
{!! $model->getDateColumns() ?? null !!}
    ];

    /**
     * {{ '@' }}var array
     */
    protected $casts = [
{!! $model->getCasts() ?? null !!}
    ];
}
