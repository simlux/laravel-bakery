use App\Models\{{ $model }};
use Illuminate\Database\Seeder;

class {{ $class }}TableSeeder extends {{ $extends }}
{
    /**
     * Run the database seeds.
     *
     * {{ '@' }}return void
     */
    public function run()
    {
        {{ $model }}::create([
            {!! $properties !!}
        ]);
    }
}
