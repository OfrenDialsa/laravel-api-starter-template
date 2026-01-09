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
