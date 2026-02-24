<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Creiamo l'utente
        $this->admin = User::factory()->create();
    }

    /**
     * @test
     */
    public function carica_il_dashboard_di_filament()
    {
        $this
            ->actingAs($this->admin)
            ->get(Filament::getPanel('admin')->getDashboardUrl())
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function caricano_tutti_i_resource_principali()
    {
        $this->actingAs($this->admin);
        $panel = Filament::getPanel('admin');

        foreach ($panel->getResources() as $resource) {
            $url = $resource::getUrl('index');

            $this
                ->get($url)
                ->assertOk()
                ->assertSeeText($resource::getNavigationLabel());
        }
    }
}
