<?php

namespace App\Http\Controllers;

use Exception;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class CrudController extends Controller
{
    
    public function showForm()
    {
        return view('project-generator');
    }
    public function generateProject(Request $request)
    {
        $moduleName = $request->input('project_name');

        // Step 1: Trim leading and trailing spaces
        $moduleName = trim($moduleName);

        // Step 2: Remove non-alphanumeric characters (except for spaces) and convert to StudlyCase
        $moduleName = str_replace(['-', '_'], ' ', $moduleName);
        $moduleName = ucwords($moduleName);
        $moduleName = str_replace(' ', '', $moduleName);
          // Convert the input string into an array

        $moduel_inputs = explode(',', $request->inputs);
        $inputs = array_map('trim', $moduel_inputs);

        $inputArray = array_values($moduel_inputs);
        
        // Display the resulting array
        // Convert the module name to the appropriate controller class name (e.g., UserController)
        $controllerClass = ucfirst($moduleName) . 'Controller';

        // Create the new controller file
        $controllerStub = $this->getControllerStub($controllerClass, $moduleName);
        $path = app_path('Http/Controllers/' . $controllerClass . '.php');
    
        if (File::exists($path)) {
            throw new Exception('Controller already exists.');
        }
    
        file_put_contents($path, $controllerStub);
    
        // Create a folder for the module views if it doesn't exist
        $moduleViewPath = resource_path('views/modules/' . strtolower($moduleName));
        if (!File::exists($moduleViewPath)) {
            File::makeDirectory($moduleViewPath, 0755, true);
        }
    
        // Create the new view files
        $createStub = File::get(base_path('resources/stubs/views/create.blade.php'));
        $updateStub = File::get(base_path('resources/stubs/views/update.blade.php'));
        $indexStub = File::get(base_path('resources/stubs/views/index.blade.php'));
    
        $modelName = ucfirst($moduleName);

        $createStub = str_replace(['{{ $modelName }}', '{{ $inputs }}'], [$modelName, $this->generateFormFields($inputs)], $createStub);
        $updateStub = str_replace(['{{ $modelName }}', '{{ $inputs }}'], [$modelName, $this->generateFormUpdateFields($inputs)], $updateStub);
        $indexStub = str_replace(['{{ $modelName }}', '{{ $inputsTable }}'], [$modelName, $this->generateIndexFields( $inputArray)], $indexStub);
        
        $createPath = $moduleViewPath . '/create.blade.php';
        $updatePath = $moduleViewPath . '/update.blade.php';
        $indexPath = $moduleViewPath . '/index.blade.php';
    
        if (File::exists($createPath) || File::exists($updatePath) || File::exists($indexPath)) {
            throw new Exception('View files already exist.');
        }
    
        file_put_contents($createPath, $createStub);
        file_put_contents($updatePath, $updateStub);
        file_put_contents($indexPath, $indexStub);
    
        // make new model
        $getModal = File::get(base_path('resources/stubs/models/model.stub'));
        $inputString = '"' . implode('","', $inputArray) . '"';
        $modelName = ucfirst($moduleName);
        $createModal = str_replace(['{{ class }}', '{{ attributes }}'], [$modelName, $inputString], $getModal);

        $model_path = app_path('Models/' . $modelName . '.php');
    
        if (File::exists($model_path)) {
            throw new Exception('Model already exists.');
        }
    
        file_put_contents($model_path, $createModal);
        // Add code to define routes for the new views
        $routes = "
            use App\Http\Controllers\\$controllerClass;
    
            // Define routes for the new views
            Route::get('" . strtolower($moduleName) . "/create', ${controllerClass}::class . '@create')->name('${moduleName}.create');
            Route::post('" . strtolower($moduleName) . "', ${controllerClass}::class . '@store')->name('${moduleName}.store');
            Route::get('" . strtolower($moduleName) . "/{model}/edit', ${controllerClass}::class . '@edit')->name('${moduleName}.edit');
            Route::put('" . strtolower($moduleName) . "/{model}', ${controllerClass}::class . '@update')->name('${moduleName}.update');
            Route::get('" . strtolower($moduleName) . "', ${controllerClass}::class . '@index')->name('${moduleName}.index');
        ";
    
        // Append the routes to the web.php file
        $routePath = base_path('routes/web.php');


        // make new migration stub
            $getMigrationStub = File::get(base_path('resources/stubs/migrations/migration.stub'));

            $className = 'Create' . ucfirst($moduleName) . 'Table';
            $tableName = Str::plural(Str::snake($moduleName));
            $columns = '';

            foreach ($inputArray as $input) {
                // Add columns for each input field (example for 'name' field)
                $columns .= "\t\t\t" . '$table->string(\'' . $input . '\');' . "\n";
                // Add other column types based on your requirements
            }

            $migrationStub = str_replace(['{{ className }}', '{{ tableName }}', '{{ columns }}'], [$className, $tableName, $columns], $getMigrationStub);

            // Generate a timestamp for the migration filename
            $timestamp = date('Y_m_d_His');

            // Save the migration file in the database/migrations folder
            $migrationFilename = $timestamp . '_create_' . $tableName . '_table.php';
            $migrationPath = database_path('migrations/' . $migrationFilename);
            file_put_contents($migrationPath, $migrationStub);
            // store routes
             file_put_contents($routePath, $routes, FILE_APPEND);
    
        // Redirect with success message
        return 'Project generated successfully, check your project folder.';
    }
    
    // Helper function to generate form fields based on the provided input names
    private function generateFormFields($inputs)
    {
        $fields = '';
        foreach ($inputs as $input) {
            $fields .= "\n\t\t<div class=\"form-group\">\n";
            $fields .= "\t\t\t<label for=\"$input\">$input:</label>\n";
            $fields .= "\t\t\t<input type=\"text\" name=\"$input\" class=\"form-control\" value=\"{{ old('$input') }}\">\n";
            $fields .= "\t\t</div>\n";
        }
        return $fields;
    }
    
      // Helper function to generate form fields based on the provided input names
      private function generateFormUpdateFields($inputs)
      {
          $fields = '';
          foreach ($inputs as $input) {
            $value_input = '$model->' . $input;
              $fields .= "\n\t\t<div class=\"form-group\">\n";
              $fields .= "\t\t\t<label for=\"$input\">$input:</label>\n";
              $fields .= "\t\t\t<input type=\"text\" name=\"$input\" class=\"form-control\" value=\"{{ old('$input',$value_input) }}\">\n";
              $fields .= "\t\t</div>\n";
          }
          return $fields;
      }

      private function generateIndexFields($inputs)
{
    $fields = '<table class="table table-striped">';
    // Generate the table header with column headings
    $fields .= '<thead><tr>';
    // foreach ($inputs as $heading) {
    //     $fields .= "<th>$heading</th>";
    // }
    // $fields .= '<th>Edit</th>';
    // $fields .= '<th>Delete</th>';
    // $fields .= '</tr></thead>';

    // // Generate the table body with data rows
    // $fields .= '<tbody>';
    // foreach ($models as $model) {
    //     $fields .= '<tr>';
    //     foreach ($inputs as $input) {
    //         $value = $model->$input;
    //         $fields .= "<td>$value</td>";
    //     }
    //     $fields .= '<td><a href="{{ route(\'' . strtolower($modelName) . '.edit\', $model->id) }}">Edit</a></td>';
    //     $fields .= '<td><button>Delete</button></td>';
    //     $fields .= '</tr>';
    // }
    $fields .= '</tbody>';

    $fields .= '</table>';

    return $fields;
}

    private function getControllerStub($controllerClass, $moduleName)
    {
        // Get the content of the simple controller stub
        $controllerStub = File::get(base_path('resources/stubs/simple_controller.stub'));

        $controllerStub = str_replace('{{ namespace }}', 'App\Http\Controllers', $controllerStub);
        $controllerStub = str_replace('{{ class }}', $controllerClass, $controllerStub);
        $controllerStub = str_replace('{{ modelName }}', $moduleName, $controllerStub);
        $controllerStub = str_replace('{{ modelNameVariable }}', strtolower($moduleName), $controllerStub);

        // Replace the placeholder with the module view path
        $moduleViewPath = 'modules/' . strtolower($moduleName);
        $indexViewRoute = ucfirst($moduleName);

        $controllerStub = str_replace('{{ moduleViewPath }}', $moduleViewPath, $controllerStub);
        $controllerStub = str_replace('{{ indexViewRoute }}', $indexViewRoute, $controllerStub);

    
        return $controllerStub;
    }
    
}
