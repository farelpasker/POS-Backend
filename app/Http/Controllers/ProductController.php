<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private $productRepo;
    private $productService;

    public function __construct(ProductRepository $productRepo, ProductService $productService){
        $this->productRepo = $productRepo;
        $this->productService = $productService;
    }

    public function index(Request $request) {
        $fields = ['id','name','thumbnail','price','category_id'];
        $products = $this->productRepo->getAll($fields);

        return response()->json([
            'message' => 'Products fetched successfully',
            'data' => ProductResource::collection($products)
        ], 200);
    }

    public function show(int $id, Request $request) {
        try {
            $fields = ['id','name','thumbnail','price','about','category_id'];
            $product = $this->productRepo->getById($id, $fields);

            return response()->json([
                'message' => 'Product fetched successfully',
                'data' => new ProductResource($product),
            ], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
    }

    public function store(ProductRequest $request) {
        DB::beginTransaction();
        try {
            $product = $this->productService->create($request->validated());
            DB::commit();
            return response()->json([
                'message' => 'Product created successfully',
                'data' => new ProductResource($product),
            ], 201);
        }catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(ProductRequest $request,int $id) {
        DB::beginTransaction();
        try {
            $product = $this->productService->update($id, $request->validated());
            DB::commit();
            return response()->json([
                'message' => 'Product updated successfully',
                'data' => new ProductResource($product),
            ], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id) {
        DB::beginTransaction();
        try {
            $this->productService->delete($id);
            DB::commit();
            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
