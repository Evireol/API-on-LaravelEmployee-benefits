<?php

namespace Tests\Feature;

 use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetPayroll()
    {
    $response = $this->get('/api/payroll');
    $response->assertStatus(200);
    }

    public function testGetEmployees()
    {
        $response = $this->get('/api/employees');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'name', 'email', 'created_at', 'updated_at'],
        ]);
    }

    public function testCreateEmployee()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'securepassword',
        ];

        $response = $this->postJson('/api/employees', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('employees', ['name' => 'John Doe']);
    }

    public function testGetEmployeeTransactions()
    {
        $employeeId = 1;

        $response = $this->get("/api/employees/{$employeeId}/transactions");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'employee_id', 'hours', 'created_at', 'updated_at'],
        ]);
    }

    public function testCreateTransaction()
    {
        $data = [
            'employee_id' => 1,
            'hours' => 8,
        ];

        $response = $this->postJson("/api/employees/1/transactions", $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', ['employee_id' => 1, 'hours' => 8]);
    }

    public function testMarkTransactionAsPaid()
    {
        $transactionId = 1;

        $response = $this->put("/api/transactions/{$transactionId}/mark-as-paid");
        $response->assertStatus(200);
        $this->assertDatabaseHas('transactions', ['id' => $transactionId, 'paid' => true]);
    }

    public function testCreateTransactionWithInvalidData()
    {
        $data = [
            'employee_id' => 1, // Missing 'hours' field
        ];

        $response = $this->postJson("/api/employees/1/transactions", $data);
        $response->assertStatus(422); // Unprocessable Entity
    }
}
