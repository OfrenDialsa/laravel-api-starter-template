<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';
    protected $description = 'Create a new module with standard structure';

    public function handle()
    {
        $module = Str::studly($this->argument('name'));
        $baseDir = app_path("Modules/$module");

        $folders = ['Controllers', 'Services', 'Repositories', 'Requests', 'Models'];
        foreach ($folders as $folder) {
            $path = $baseDir . '/' . $folder;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }

        $routesFile = $baseDir . "/routes.php";
        if (!file_exists($routesFile)) {
            $moduleLower = strtolower($module);
            $routesContent = "<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| $module Module Routes
|--------------------------------------------------------------------------
*/

Route::prefix('$moduleLower')->group(function () {
    // Route::get('/', [{$module}Controller::class, 'index']);
});
";

            file_put_contents($routesFile, $routesContent);
        }

        $controllerTemplate = "<?php

namespace App\\Modules\\$module\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$module}Controller extends Controller
{
    public function index()
    {
        return response()->json(['message' => '$module module works!']);
    }
};
";
        file_put_contents($baseDir . "/Controllers/{$module}Controller.php", $controllerTemplate);

        $serviceTemplate = "<?php

namespace App\\Modules\\$module\\Services;

class {$module}Service
{
    public function example()
    {
        return 'Service for $module';
    }
};
";
        file_put_contents($baseDir . "/Services/{$module}Service.php", $serviceTemplate);

        $repositoryTemplate = "<?php

namespace App\\Modules\\$module\\Repositories;

use App\\Modules\\$module\\Models\\$module;

class {$module}Repository
{
    public function all()
    {
        return $module::all();
    }
};
";
        file_put_contents($baseDir . "/Repositories/{$module}Repository.php", $repositoryTemplate);

        $modelTemplate = "<?php

namespace App\\Modules\\$module\\Models;

use Illuminate\Database\Eloquent\Model;

class $module extends Model
{
    protected \$table = '" . Str::snake(Str::plural($module)) . "';
    protected \$guarded = [];
};
";
        file_put_contents($baseDir . "/Models/{$module}.php", $modelTemplate);

        $requestTemplate =
            "<?php

namespace App\\Modules\\$module\\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{$module}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // TODO: Add validation rules
        ];
    }
};";
        file_put_contents($baseDir . "/Requests/Store{$module}Request.php", $requestTemplate);

        $tableName = Str::snake(Str::pluralStudly($module));
        $timestamp = now()->format('Y_m_d_His');

        $migrationTemplate = "<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('$tableName');
    }
};
";
        file_put_contents(
            database_path("migrations/{$timestamp}_create_{$tableName}_table.php"),
            $migrationTemplate
        );

        $factoryTemplate = "<?php

namespace Database\\Factories;

use Illuminate\\Database\\Eloquent\\Factories\\Factory;
use App\\Modules\\$module\\Models\\$module;

/**
 * @extends Factory<$module>
 */
class {$module}Factory extends Factory
{
    protected \$model = $module::class;

    public function definition(): array
    {
        return [
            'name' => \$this->faker->name(),
        ];
    }
}
";

        file_put_contents(database_path("factories/{$module}Factory.php"), $factoryTemplate);

        $seederTemplate = "<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Modules\\$module\\Models\\$module;

class {$module}Seeder extends Seeder
{
    public function run(): void
    {
        $module::factory()->count(10)->create();
    }
}
";
        file_put_contents(database_path("seeders/{$module}Seeder.php"), $seederTemplate);

        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        $databaseSeederContent = file_get_contents($databaseSeederPath);

        $callLine = "        \$this->call(\\Database\\Seeders\\{$module}Seeder::class);\n";

        if (!str_contains($databaseSeederContent, $callLine)) {
            $databaseSeederContent = preg_replace(
                '/public function run\(\): void\s*\{\n/',
                "public function run(): void\n{\n$callLine",
                $databaseSeederContent
            );

            file_put_contents($databaseSeederPath, $databaseSeederContent);
        }

        $testBaseFeature = base_path("tests/Feature/Modules/$module");
        $testBaseUnit = base_path("tests/Unit/Modules/$module");

        if (!file_exists($testBaseFeature)) {
            mkdir($testBaseFeature, 0755, true);
        }

        if (!file_exists($testBaseUnit)) {
            mkdir($testBaseUnit, 0755, true);
        }

        $featureTestTemplate = "<?php

namespace Tests\\Feature\\Modules\\$module;

use Tests\\TestCase;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;

class {$module}ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_{$module}_index_endpoint_returns_success()
    {
        \$response = \$this->getJson('/api/" . Str::kebab($module) . "');

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'message'
            ]);
    }
}
";
        file_put_contents(
            base_path("tests/Feature/Modules/$module/{$module}ApiTest.php"),
            $featureTestTemplate
        );

        $serviceTestTemplate = "<?php

namespace Tests\\Unit\\Modules\\$module;

use Tests\\TestCase;
use App\\Modules\\$module\\Services\\{$module}Service;

class {$module}ServiceTest extends TestCase
{
    public function test_service_example_method_returns_string()
    {
        \$service = new {$module}Service();

        \$result = \$service->example();

        \$this->assertEquals('Service for $module', \$result);
    }
}
";
        file_put_contents(
            base_path("tests/Unit/Modules/$module/{$module}ServiceTest.php"),
            $serviceTestTemplate
        );
        $repositoryTestTemplate = "<?php

namespace Tests\\Unit\\Modules\\$module;

use Tests\\TestCase;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use App\\Modules\\$module\\Repositories\\{$module}Repository;
use App\\Modules\\$module\\Models\\$module;

class {$module}RepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_can_get_all_data()
    {
        $module::factory()->count(3)->create();

        \$repo = new {$module}Repository();
        \$result = \$repo->all();

        \$this->assertCount(3, \$result);
    }
}
";
        file_put_contents(
            base_path("tests/Unit/Modules/$module/{$module}RepositoryTest.php"),
            $repositoryTestTemplate
        );


        $this->info("Module $module created successfully!");
    }
}
