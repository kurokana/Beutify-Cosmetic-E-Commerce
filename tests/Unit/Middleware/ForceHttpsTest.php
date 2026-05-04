<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ForceHttps;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * ForceHttps Middleware Unit Tests
 *
 * Tests for Requirement 14.2: HTTPS enforcement
 */
class ForceHttpsTest extends TestCase
{
    private ForceHttps $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ForceHttps();
    }

    /**
     * Test middleware redirects HTTP to HTTPS in production
     */
    public function test_redirects_http_to_https_in_production(): void
    {
        // Set app environment to production
        app()->detectEnvironment(function () {
            return 'production';
        });

        $request = Request::create('http://example.com/catalog', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Should not reach here');
        });

        $this->assertEquals(301, $response->getStatusCode());
        // Laravel's redirect()->secure() will use the configured APP_URL
        // Just verify it redirects to HTTPS protocol
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://', $location);
        $this->assertStringContainsString('/catalog', $location);
    }

    /**
     * Test middleware preserves query parameters in redirect
     */
    public function test_preserves_query_parameters_in_redirect(): void
    {
        // Set app environment to production
        app()->detectEnvironment(function () {
            return 'production';
        });

        $request = Request::create('http://example.com/search?q=lipstik&sort=price', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Should not reach here');
        });

        $this->assertEquals(301, $response->getStatusCode());
        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://', $location);
        $this->assertStringContainsString('/search', $location);
        $this->assertStringContainsString('q=lipstik', $location);
        $this->assertStringContainsString('sort=price', $location);
    }

    /**
     * Test middleware allows HTTPS requests in production
     */
    public function test_allows_https_requests_in_production(): void
    {
        config(['app.env' => 'production']);

        $request = Request::create('https://example.com/catalog', 'GET');
        $request->server->set('HTTPS', 'on');

        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test middleware does not redirect in local environment
     */
    public function test_does_not_redirect_in_local_environment(): void
    {
        config(['app.env' => 'local']);

        $request = Request::create('http://localhost/catalog', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test middleware does not redirect in development environment
     */
    public function test_does_not_redirect_in_development_environment(): void
    {
        config(['app.env' => 'development']);

        $request = Request::create('http://localhost/catalog', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test middleware does not redirect in testing environment
     */
    public function test_does_not_redirect_in_testing_environment(): void
    {
        config(['app.env' => 'testing']);

        $request = Request::create('http://localhost/catalog', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test middleware uses 301 (permanent redirect) status code
     */
    public function test_uses_permanent_redirect_status_code(): void
    {
        // Set app environment to production
        app()->detectEnvironment(function () {
            return 'production';
        });

        $request = Request::create('http://example.com/catalog', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Should not reach here');
        });

        // 301 = Moved Permanently (better for SEO than 302)
        $this->assertEquals(301, $response->getStatusCode());
    }
}
