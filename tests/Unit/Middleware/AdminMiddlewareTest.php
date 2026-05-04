<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * AdminMiddleware Unit Tests
 *
 * Tests for Requirement 14.5: Admin role verification
 */
class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private AdminMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new AdminMiddleware();
    }

    /**
     * Test middleware redirects unauthenticated users to login
     */
    public function test_redirects_unauthenticated_users_to_login(): void
    {
        $request = Request::create('/admin/dashboard', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Should not reach here');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(str_contains($response->headers->get('Location'), 'login'));
    }

    /**
     * Test middleware blocks non-admin users with 403
     */
    public function test_blocks_non_admin_users(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        Auth::login($customer);

        $request = Request::create('/admin/dashboard', 'GET');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Akses ditolak');

        $this->middleware->handle($request, function () {
            return response('Should not reach here');
        });
    }

    /**
     * Test middleware allows admin users
     */
    public function test_allows_admin_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Auth::login($admin);

        $request = Request::create('/admin/dashboard', 'GET');

        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }

    /**
     * Test middleware verifies role on every request
     */
    public function test_verifies_role_on_every_request(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        Auth::login($user);

        $request = Request::create('/admin/dashboard', 'GET');

        // First request should fail
        try {
            $this->middleware->handle($request, function () {
                return response('Should not reach here');
            });
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }

        // Change user role to admin
        $user->update(['role' => 'admin']);
        Auth::login($user); // Re-authenticate

        // Second request should succeed
        $response = $this->middleware->handle($request, function () {
            return response('Success');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}
