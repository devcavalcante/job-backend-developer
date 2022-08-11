<?php

namespace App\Console\Commands;

use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ImportProductsCommand extends Command
{
    const URL = 'https://fakestoreapi.com/products';

    public function __construct(private ProductRepository $productRepository)
    {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:import {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importaçâo de produtos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Http::get(self::URL);

        if($this->option('id')) {
            return $this->importProductById($this->option('id'));
        }
        
        return $this->importProducts($products->json());
    }

    private function importProductById(string $id)
    {
        $product = Http::get(self::URL.'/'.$id);
        $findProduct = $this->productRepository->findByFilters(['id' => $id]);
        $product = $this->formatData($product->json()); 

        if($findProduct->isNotEmpty()) {
            $confirm = $this->confirm('Esse produto ja foi importado, deseja atualizar as informaçôes?');
            return $confirm ? $this->updateProduct($product, $id) : $this->info('Açâo cancelada');
        }

        $this->productRepository->create($product);
        return $this->info(sprintf('Produto com id %s importado com sucesso', $id));
    }

    private function importProducts(array $products)
    {
        foreach($products as $product)
        {
            $product = $this->formatData($product);  
            $productId = Arr::get($product, 'id');
            $findProduct = $this->productRepository->findByFilters(['id' => $productId]);

            if($findProduct->isNotEmpty()) {
                $confirm = $this->confirm(sprintf('O produto %s ja foi importado deseja reimportar?', $productId));
                 
                if(!$confirm) {
                    return $this->info('Açâo cancelada');
                }
                
                $this->productRepository->destroy($productId);
            }

            $this->productRepository->create($product);
        }
        $this->info('Produtos importados com sucesso');
    }

    private function formatData(array $product): array
    {
        $name = Arr::get($product, 'title');
        $imageUrl = Arr::get($product, 'image');
        Arr::set($product, 'name', $name);
        Arr::set($product, 'image_url', $imageUrl);
        return $product;
    }

    private function updateProduct(array $data, string $id)
    {
        $this->productRepository->update($data, $id);
        $this->info('Produto atualizado com sucesso');
    }
}
