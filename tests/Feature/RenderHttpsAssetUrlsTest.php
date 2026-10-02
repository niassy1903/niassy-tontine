<?php

namespace Tests\Feature;

use Tests\TestCase;

class RenderHttpsAssetUrlsTest extends TestCase
{
    public function test_asset_urls_use_https_when_forwarded_by_render(): void
    {
        $response = $this->withHeader('X-Forwarded-Proto', 'https')->get('/');

        $response->assertSee('href="https://localhost:8000/css/niassy.css"', false);
        $response->assertSee('href="https://localhost:8000/build/assets/app-', false);
    }
}
