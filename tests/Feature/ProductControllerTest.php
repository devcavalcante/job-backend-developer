<?php

namespace Tests\Feature;

use App\Exceptions\NotFoundException;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testShouldListaAll()
    {
        $response = $this->get('api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(20);
    }

    public function testShouldListOne(){
        $product = Product::first();

        $response = $this->get(sprintf('api/product/%s', $product->id));
        $data = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $this->assertEquals($product->toArray(), $data);
    }

    public function testShouldGetNotFoundMessage(){
        $this->get(sprintf('api/product/%s', 100))
        ->assertStatus(404)
        ->assertSeeText('Produto nâo encontrado');
    }

    public function testShouldCreate()
    {
        $payload = [
            "name" => "product name",
            "price" => 109.95,
            "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...",
            "category" => "test",
            "image_url" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
        ];

        $response = $this->post('api/products', $payload);
        $data = json_decode($response->getContent(), true);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $data);
    }

    public function testShouldCreateFails()
    {
        $payload = [
            "price" => 109.95,
            "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...",
            "category" => "test",
            "image_url" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
        ];

        $this->post('api/products', $payload)
        ->assertSessionHasErrors('name')
        ->assertStatus(302);

        $this->assertDatabaseMissing('products', $payload);
    }
}
