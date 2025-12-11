<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageLoadRedirect extends TestCase
{
    /**
     * A basic test example.
     * In this example we are testing the main site pages route
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {        
        $response = $this->get('/'); // If redirection is expected and okay
        $response->assertStatus(302);
        
        // Or, to follow a redirect and then check
        $response->assertRedirect('/pages');
        $response = $this->get('/pages');
        $response->assertStatus(200);
    }
}
