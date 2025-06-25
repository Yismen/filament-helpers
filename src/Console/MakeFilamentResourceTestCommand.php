<?php

namespace Dainsys\FilamentHelpers\Console;

use Dainsys\FilamentHelpers\Traits\HasFilamentPanels;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\select;

class MakeFilamentResourceTestCommand extends Command implements PromptsForMissingInput
{
    use HasFilamentPanels;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dainsys:make-filament-resource-test 
        {model : The model name corresponding to the filament resource} 
        {panel : Filament panel name} 
        {--F|force}';

    protected $model;

    protected $model_as_lowercase;

    protected $panel;

    protected $panel_as_title;

    protected Filesystem $filesystem;

    protected string $stub_file_name = 'make-filament-resource-file.stub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->model = str($this->argument('model'))->trim()->studly()->toString();
        $this->panel = str($this->argument('panel'))->trim()->snake()->toString();

        $this->model_as_lowercase = str($this->model)->snake();
        $this->panel_as_title = str($this->panel)->studly();

        $this->createFile();
    }

    protected function createFile(): bool
    {
        $dir_path = base_path("tests\\Feature\\Filament\\{$this->panel_as_title}\\Resources");
        $file_path = $dir_path."\\{$this->model}ResourceTest.php";
        $dir_path = str($dir_path)->trim('\\')->replace('\\', '/');
        $file_path = str($file_path)->replace('\\', '/');

        if (file_exists($file_path) && ! $this->option('force')) {
            if (! $this->confirm("File {$file_path} already exists! Do you want to override it?")) {
                $this->warn('Existing file not created!');

                return false;
            }
        }

        $content = $this->getStubContent();

        $this->filesystem->ensureDirectoryExists(
            $dir_path,
            0755
        );

        $this->filesystem->put($file_path, $content);

        $this->info("File {$file_path} created successfully!");

        return true;
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'panel' => fn () => select(
                label: 'Select a panel',
                options: $this->getFilamentPanels()
            ),
        ];
    }

    protected function getStubContent(): string
    {
        $stub_path = config('dainsys-filament-helpers.stubs_publishes_dir', 'stubs/dainsys/').$this->stub_file_name;
        // allow override via published stub
        $custom = base_path($stub_path);

        $content = $this->filesystem->exists($custom)
            ? $this->filesystem->get($custom)
            : $this->filesystem->get(__DIR__.'/../../stubs/'.$this->stub_file_name);

        return str($content)
            ->replace('{{ panel }}', $this->panel)
            ->replace('{{ Model }}', $this->model)
            ->replace('{{ model_as_lowercase }}', $this->model_as_lowercase)
            ->replace('{{ panel_as_title }}', $this->panel_as_title)
            ->toString();
    }
}
