<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertSee('MoraLenz')
            ->assertSee('Upcoming Events')
            ->assertSee('Media Awards 2025')
            ->assertSee('Photography Pillar')
            ->assertSee('Photography Workshop');
    }

    public function test_contact_form_persists_messages(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'General Inquiry',
            'message' => 'I would like to join Mora Lenz.',
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'jane@example.com',
            'subject' => 'General Inquiry',
        ]);
    }

    public function test_admin_upload_api_stores_images_with_bearer_token(): void
    {
        config(['services.admin_upload.token' => 'test-upload-token']);

        $response = $this->postJson('/api/admin/uploads', [
            'type' => 'gallery',
            'image' => UploadedFile::fake()->image('gallery.jpg', 640, 480),
        ], [
            'Authorization' => 'Bearer test-upload-token',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['path', 'url']);

        $path = public_path(ltrim($response->json('path'), '/'));

        $this->assertFileExists($path);

        File::delete($path);
    }
}
