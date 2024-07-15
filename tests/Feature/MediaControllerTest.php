<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Media;
use App\Models\Genre;
use Tests\TestCase;

class MediaControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function testUpdateMediaWithFileUpload()
    {
        Storage::fake("local");

        $media = Media::create([
            'name' => 'Old Media Name',
            'genre_id' => null,
            'description' => 'Old Description',
            'thumbnail' => null,
            'media_type' => 'movie',
        ]);

        // Create a sample genre record
        $genre = Genre::create([
            'name' => 'Sample Genre',
            'description' => 'Sample Description',
        ]);

        // Simulate a file upload
        $file = UploadedFile::fake()->image('thumbnail.jpg');

        $data = [
            'name' => 'New Media Name',
            'genre_id' => $genre->id,
            'description' => 'New Description',
            'thumbnail' => $file,
            'media_type' => 'music',
        ];

        // Send the update request
        $response = $this->json('PUT', route('media.update', $media->id), $data);
        if ($response->status() !== 200) {
            info('Response Body: '.$response->json());
            dump($response->getContent()); 
        }
        $media->refresh();
        $response->assertStatus(200);

        // Assert the response JSON structure
        $response->assertJson([
            'message' => 'Media updated successfully',
            'media' => [
                'id' => $media->id,
                'name' => 'New Media Name',
                'genre_id' => $genre->id,
                'description' => 'New Description',
                'media_type' => 'music',
            ],
        ]);

        Storage::disk("local")->assertExists("media_thumbnails/".$file->hashName());
        // Assertion Update was successful
        $this->assertEquals('New Media Name', $media->name);
        $this->assertEquals($genre->id, $media->genre_id);
        $this->assertEquals('New Description', $media->description);
        $this->assertEquals("media_thumbnails/".$file->hashName(), $media->thumbnail);
        $this->assertEquals('music', $media->media_type);
    }
}
