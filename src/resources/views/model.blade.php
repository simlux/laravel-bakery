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
    {!! $constants !!}

    /**
     * {{ '@' }}var string
     */
    protected $table = self::TABLE;

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
