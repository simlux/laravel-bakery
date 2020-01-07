use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
{{ $uses }}

class Create{{ $class }}Table extends {{ $extends }}
{
    /**
     * Run the migrations.
     *
     * {{ '@' }}return void
     */
    public function up()
    {
        Schema::create('{{ $table }}', function (Blueprint $table) {
            {!! $columns !!}
@if($indexes)

            // indexes
            {!! $indexes !!}
@endif
@if($foreignKeys)

            // foreign keys
            {!! $foreignKeys !!}
@endif
        });
    }

    /**
     * Reverse the migrations.
     *
     * {{ '@' }}return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $table }}');
    }
}
