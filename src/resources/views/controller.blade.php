namespace {{ $namespace }};

use Illuminate\Http\Request;
{{ $uses ?? null }}

/**
 * Class {{ $model }}Controller
 *
 * {{ '@' }}package {{ $namespace }}
 */
class {{ $model }}Controller extends Controller
{
    {!! $methods !!}
}
