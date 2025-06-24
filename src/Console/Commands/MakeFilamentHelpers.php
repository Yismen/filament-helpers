<?php

namespace Dainsys\FilamentHelpers\Console\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeFilamentHelpers extends Command implements PromptsForMissingInput
{


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
    protected $file;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $file_path = $dir_path . "\\{$this->model}ResourceTest.php";

        if(file_exists($file_path) && ! $this->option('force')) {
            if(! $this->confirm("File {$file_path} already exists! Do you want to override it?"))
            {
                // $this->error("file {$file_path} already exists!");
                $this->warn('Existing file not created!');

                return false;
            }
        }

        // create directory if it does not exists

        $content = str($this->getStub())
            ->replace("{{ panel }}", $this->panel)
            ->replace("{{ Model }}", $this->model)
            ->replace("{{ model_as_lowercase }}", $this->model_as_lowercase)
            ->replace("{{ panel_as_title }}", $this->panel_as_title)
            ->toString()
            ;
        
        $filesystem = app(Filesystem::class);

        $filesystem->ensureDirectoryExists(
            pathinfo($dir_path, PATHINFO_DIRNAME),
        );

        $filesystem->put($file_path, $content);

        // Storage::put($file_path, $content);

        $this->info("File {$file_path} created successfully!");

        return true;
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        $panels = [];

        foreach (\Filament\Facades\Filament::getPanels() as $this->panel) {
            $panels[] = $this->panel->getId();
        }

        return [
            'panel' => fn() => select(
                label: 'Select a panel',
                options: $panels
            )
        ];
    }

    protected function getFilamentPanels(): array
    {
        return \Filament\Facades\Filament::getPanels();
    }

    protected function getStub(): string
    {
        return "<?php

use App\Models\User;
use App\Models\{{ Model }};
use Livewire\Livewire;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Permission;
use App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource;

describe('{{ Model }} Resource', function () {
    beforeEach(function () {
        Filament::setCurrentPanel(
            Filament::getPanel('{{ panel }}')
        );
        // Create a basic user for auth
        \$this->user = User::factory()->create();
        // Create a user who can manage posts
        \$this->permitted_user = User::factory()->create();

        \$this->model = {{ Model }}::factory()->create();

        \$this->routes = [
            'index' => {{ Model }}Resource::getUrl('index'),
            'create' => {{ Model }}Resource::getUrl('create'),
            'edit' => {{ Model }}Resource::getUrl('edit', ['record' => \$this->model->getRouteKey()]),
            'view' => {{ Model }}Resource::getUrl('view', ['record' => \$this->model->getRouteKey()]),
        ];
    });

    describe('authentication', function() {
        it('redirects guests to login page', function (\$route) {
            \$this->get(\$this->routes[\$route])
                ->assertRedirect(route('filament.{{ panel }}.auth.login'));
        })->with([
            'index',
            'create',
            'edit',
            'view',
        ]);

        test('user without permission gets 403', function (\$route) {
            \$this->actingAs(\$this->user)
            ->get(\$this->routes[\$route])
            ->assertForbidden();
        })->with([
            'index',
            'create',
            'edit',
            'view',
        ]);
    });

    describe('authorization', function() {
        beforeEach(function() {
            \$permissions = [
                'index' => 'viewAny',
                'create' => 'create',
                'edit' => 'update',
                'view' => 'view',
            ];
            // Assuming you use Spatie permissions and filament-shield:
            foreach (\$permissions as \$route => \$permission) {
                // \$permission = str(\$permission)->append('{{ Model }}')->snake()->toString();
                Permission::create([
                    'name' => \$permission,
                    'guard_name' => 'web',
                ]);
                \$this->permitted_user->givePermissionTo(\$permission);
            }

        });

        test('authorized user can see {{ Model }}s index and listing', function () {
            \$this->actingAs(\$this->permitted_user);

            // Using Livewire list component
            Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\List{{ Model }}s::class)
                ->assertSee('{{ Model }}s');
        });

        test('authorized user can create a {{ model_as_lowercase }}', function () {
            \$this->actingAs(\$this->permitted_user);

            \$data = [
                'name'   => 'New Pest {{ Model }}',
                'invoice_template' => 'Content of the new post',
            ];

            Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\Create{{ Model }}::class)
                ->fillForm(\$data)
                ->call('create')
                ->assertHasNoErrors(); // adjust if your redirect differs

            \$this->assertDatabaseHas({{ Model }}::class, \$data);
        });

        test('authorized user can update a {{ model_as_lowercase }}', function () {
            \$this->actingAs(\$this->permitted_user);

            \$data = [
                'name'   => 'Updated Name',
            ];

            Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\Edit{{ Model }}::class, [
                    'record' => \$this->model->getKey(),
                ])
                ->fillForm(\$data)
                ->call('save')
                ->assertHasNoErrors();

            \$this->model->refresh();
            expect(\$this->model->name)->toBe(\$data['name']);
            expect(\$this->model->invoice_template)->toBe(\$data['invoice_template']);
        });

        test('detail page displays the {{ model_as_lowercase }}', function () {
            \$this->actingAs(\$this->permitted_user);

            Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\View{{ Model }}::class, [
                    'record' => \$this->model->getKey(),
                ])
                ->assertOk()
                // ->assertViewHas(['name' => \$this->model->name])
                // ->assertSee(\$this->model->name)
                ;
        });

        describe('valications', function() {
            test('validation errors are shown for required fields', function (\$field) {
                \$this->actingAs(\$this->permitted_user);

                Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\Create{{ Model }}::class)
                    // leave out required 'name'
                    ->fillForm([
                        \$field => null,
                    ])
                    ->call('create')
                    ->assertHasErrors([
                        \"data.{\$field}\"   => 'required',
                    ]);

                    Livewire::test(\App\Filament\{{ panel_as_title }}\Resources\{{ Model }}Resource\Pages\Edit{{ Model }}::class, [
                        'record' => \$this->model->getKey(),
                    ])
                    ->fillForm([
                        \$field => null,
                    ])
                    ->call('save')
                    ->assertHasErrors([
                        \"data.{\$field}\"   => 'required',
                    ]);
            })->with([
                'name'
            ]);
        });
    });
});

it('shows correct navigation sort', function () {
    expect(AgentResource::getNavigationSort())->toBe(3);
});";

    }
}
