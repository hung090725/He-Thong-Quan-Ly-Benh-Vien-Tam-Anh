<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ReceptionWorkflowIntegrityTest extends TestCase
{
    protected User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receptionist = User::where('role', User::ROLE_RECEPTIONIST)->first()
            ?? User::factory()->create([
                'email' => 'receptionist_integrity_' . uniqid() . '@tamanh.vn',
                'role' => User::ROLE_RECEPTIONIST,
            ]);
    }

    /**
     * Test admin sidebar renders professional medical layout with unaccented search and zero personal tribute.
     */
    public function test_sidebar_contains_search_and_zero_personal_tribute(): void
    {
        $response = $this->actingAs($this->receptionist)->get(route('reception.appointments.index'));

        $response->assertStatus(200);

        // Đảm bảo có thanh tìm kiếm sidebar nhanh
        $response->assertSee('id="sidebarSearchInput"', false);
        $response->assertSee('removeVietnameseTones', false);

        // Đảm bảo không còn bất kỳ huy hiệu vinh danh hay modal cá nhân nào
        $response->assertDontSee('ĐỒ ÁN XUẤT SẮC');
        $response->assertDontSee('projectContributionModal');
        $response->assertDontSee('Huy Hiệu Đồ Án Xuất Sắc');
        $response->assertDontSee('Nút Báo Cáo Đóng Góp');
        $response->assertDontSee('Mở GitHub PR #6');

        // Đảm bảo menu hiển thị chuẩn mực và sạch sẽ
        $response->assertSee('Tiếp nhận');
        $response->assertSee('Quản lý Bệnh nhân');
        $response->assertSee('Quầy Dược Phát Thuốc');
        $response->assertSee('Giường Ngoại Trú');
    }

    /**
     * Test all core receptionist routes return 200 OK.
     */
    public function test_all_core_receptionist_routes_return_ok(): void
    {
        $routes = [
            'reception.appointments.index',
            'reception.patients.index',
            'reception.invoices.index',
            'reception.pharmacy.index',
            'reception.pharmacy.inventory.index',
            'reception.bed-board.index',
            'reception.lab-orders.index',
            'reception.blood-transfusions.index',
            'reception.code-red.index',
            'reception.news2-rrt.index',
            'reception.operating-room.index',
            'reception.autonomous-logistics-esg.index',
            'reception.analytics.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->receptionist)->get(route($routeName));
            $this->assertTrue(
                in_array($response->getStatusCode(), [200, 302]),
                "Route [{$routeName}] must return 200 OK or valid redirect, got " . $response->getStatusCode()
            );
        }
    }

    /**
     * Test public kiosk page renders and operates cleanly.
     */
    public function test_public_kiosk_renders_and_operates(): void
    {
        $response = $this->get('/kiosk');
        $response->assertStatus(200);
        $response->assertSee('Kiosk');
        $response->assertSee('Tự Phục Vụ');
    }
}
