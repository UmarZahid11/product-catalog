<?php
 
namespace App\Providers;
 
use Illuminate\Support\ServiceProvider;
use App\Repositories\ProductRepository;
use App\Interfaces\ProductRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Interfaces\CategoryRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }
}