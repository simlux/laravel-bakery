namespace {{ $namespace }};

{{ $uses }}

/**
 * Class {{ $class }}Repository
 *
 * {{ '@' }}package {{ $namespace }}
 */
class {{ $class }}Repository @if($extends) extends {{ $extends }} @endif
{

}