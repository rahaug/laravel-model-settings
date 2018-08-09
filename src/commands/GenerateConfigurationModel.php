<?php

namespace RolfHaug\ModelSettings\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use RolfHaug\ModelSettings\Compiler;

class GenerateConfigurationModel extends Command
{
    const MIGRATION_TEMPLATE = '../templates/Migration.txt';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model-settings {--model=} {--namespace=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new Settings Model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if( ! $this->option('model')) {
            $this->error("Please provide a model --model=resource. Aborting!");
        }
        // Get resource
        $resource = strtolower($this->option('model'));

        $this->info("Create Model Settings for " . studly_case($resource));

        if($this->option('namespace')) {
            $modelNamespace = $this->option('namespace');
        }else {
            $modelNamespace = "App";
        }

        // Validate resource and grab an instance
        $model = $this->makeModel($resource, $modelNamespace);

        // Return if model doesn't exists. Error is thrown in the makeModel method
        if( ! $model) {
            return;
        }

        $this->info("Creating model settings for " . get_class($model));

        // Create Migration
        $settingsModel = strtolower(class_basename($model)) . "_settings";

        $this->makeMigration($model, $settingsModel);

        // Migrate
        $this->call('migrate');

        // Create Settings Model
        $this->info("Creating Settings Model");
        $this->call("make:model", ['name' => studly_case($settingsModel)]);

        $this->info("Settings Model for " . get_class($model) . " was successfully Created.");

        $this->warn("Please import and use the Settings trait on the " . get_class($model) . " to complete the integration");

    }

    protected function makeModel($resource, $modelNamespace)
    {
        try {
            $modelNamespace = addslashes(rtrim(studly_case($modelNamespace), "\\"));
            $model = App::make("\\" . $modelNamespace . "\\" . studly_case($resource));
        }

        catch(\Exception $e)
        {
            $this->error($e->getMessage() . ". Aborting!");
            return null;
        }

        if( ! is_subclass_of($model, 'Illuminate\Database\Eloquent\Model'))
        {
            $this->error(studly_case($resource) . " is not an Eloquent model. Aborting!");
            return null;
        }

        // Check if the settings model already exists
        if( class_exists($modelNamespace . studly_case($model->getTable() . "_settings"))) {
            $this->error("Settings Model for " . studly_case($resource) . " already exists. Aborting!");
            return null;
        }

        return $model;
    }

    protected function makeMigration($model, $settingsModel)
    {
        // Template data
        $data = [
            'table' =>$settingsModel,
            'parent_table' => $model->getTable(),
            'key' => $model->getKeyName(),
            'foreign_key' => $model->getForeignKey(),
            'class' => studly_case("create_" . $settingsModel . "_table")
        ];

        // Compile template
        $template = file_get_contents(__DIR__ . "/" . self::MIGRATION_TEMPLATE);
        $this->info("Compiling migration template");
        $migration = Compiler::compile($template, $data);

        // Save migration
        $path = database_path() . "/migrations/" . date('Y_m_d_His_') . "create_" . $settingsModel . "_table.php";

        $this->info("Creating migration");
        file_put_contents($path, $migration);
    }
}