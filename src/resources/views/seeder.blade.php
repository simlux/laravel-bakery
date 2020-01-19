use App\Models\{{ $model }};
use Illuminate\Database\Seeder;

class {{ $class }}TableSeeder extends {{ $extends }}
{
    /**
     * Run the database seeds.
     *
     * {{ '@' }}return void
     */
    public function run(): void
    {
        {{ $model }}::create([
            {!! $properties !!}
        ]);
    }
    {{ $csvSeederStub ?? null }}
}
