<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_submissions_are_rate_limited_without_extra_records(): void
    {
        $payload = ['name' => 'Test', 'email' => 'test@example.com', 'subject' => 'Question', 'message' => 'Hello'];
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/contact', $payload)->assertRedirect();
        }

        $this->post('/contact', $payload)->assertStatus(429);
        $this->assertDatabaseCount('contact_messages', 5);
    }

    public function test_upload_requires_the_correct_token(): void
    {
        config(['services.admin_upload.token' => 'test-token']);

        $this->postJson('/api/admin/uploads', [], ['Authorization' => 'Bearer wrong-token'])
            ->assertUnauthorized();
    }

    public function test_upload_is_disabled_without_a_token(): void
    {
        config(['services.admin_upload.token' => '']);

        $this->postJson('/api/admin/uploads')->assertStatus(503);
    }

    public function test_upload_rejects_script_files(): void
    {
        config(['services.admin_upload.token' => 'test-token']);

        $this->postJson('/api/admin/uploads', [
            'type' => 'gallery',
            'image' => UploadedFile::fake()->createWithContent('attack.php', '<?php echo "unsafe";'),
        ], ['Authorization' => 'Bearer test-token'])
            ->assertUnprocessable()->assertJsonValidationErrors('image');
    }
}
