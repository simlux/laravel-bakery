use App\Models\{{ $model }};
use Faker\Generator as Faker;

/** {{ '@' }}var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define({{ $model }}::class, function (Faker $faker) {
    return [
        {!! $properties !!}
    ];
});
