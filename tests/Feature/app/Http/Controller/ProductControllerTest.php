<?php

namespace Tests\Feature\app\Http\Controller;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

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
    public function testShouldListAll()
    {
        $response = $this->get('api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(20);
    }

    public function testShouldListOne()
    {
        $product = Product::first();

        $response = $this->get(sprintf('api/product/%s', $product->id));
        $data = json_decode($response->getContent(), true);
        $response->assertStatus(200);
        $this->assertEquals($product->toArray(), $data);
    }

    public function testShouldGetNotFoundMessage()
    {
        $this->get(sprintf('api/product/%s', 100))
            ->assertStatus(404)
            ->assertSeeText('Produto nâo encontrado');
    }

    public function testShouldFindByNameCategory()
    {
        $product = Product::first();

        $response = $this->get(
            sprintf('api/products?name=%s&category=%s', $product->name, $product->category)
        );

        $response->assertStatus(200);
        $this->assertEquals(json_encode([$product]), $response->getContent());
    }

    public function testShouldFindByCategory()
    {
        $product = Product::first();

        $response = $this->get(
            sprintf('api/products?category=%s', $product->category)
        );
        
        $response->assertStatus(200);
        $this->assertEquals(json_encode([$product]), $response->getContent());
    }

    public function testShouldFindByImageNotNull()
    {
        $response = $this->get('api/products?image_url=true');
        
        $response->assertStatus(200);
        $this->assertImageNotNull(json_decode($response->getContent(), true));
    }

    public function testShouldFindByImageNull()
    {
        $response = $this->get('api/products?image_url=false');
        $response->assertStatus(200);
        $this->assertImageNull(json_decode($response->getContent(), true));
    }

    public function testShouldEmptyFilters()
    {
        $response = $this->get('api/products?teste=teste');
        $response->assertStatus(200);
        $this->assertEquals([], json_decode($response->getContent(), true));
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

    public function testShouldUpdate()
    {
        $product = Product::first();

        $payload = [
            "price" => 200.95,
        ];

        $response = $this->put(sprintf('api/product/%s', $product->id), $payload);
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $payload);
    }

    public function testShouldDestroy()
    {
        $product = Product::first();

        $response = $this->delete(sprintf('api/product/%s', $product->id));
        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', $product->toArray());
    }

    private function assertImageNotNull(array $data)
    {
        foreach($data as $item) {
            $this->assertNotEmpty($item['image_url']);
        }
    }

    private function assertImageNull(array $data)
    {
        foreach($data as $item) {
            $this->assertEmpty($item['image_url']);
        }
    }
}
