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
            file_put_contents($routesFile, "<?php\n\n// Routes for $module module\n");
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

        $requestTemplate = "<?php

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
};
";
        file_put_contents($baseDir . "/Requests/Store{$module}Request.php", $requestTemplate);

        $this->info("Module $module created successfully with Controller, Service, Repository, Requests, Models, and routes.php!");
    }
}
