<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Storage;

class ProductService {
    private $productRepo;

    public function __construct(ProductRepository $productRepo){
        $this->productRepo = $productRepo;
    }

    private function uploadThumbnail(UploadedFile $file) {
        return $file->store('products', 'public');
    }

    private function deleteThumbnail(string $Photopath) {
        $relativePath = 'products/'. basename($Photopath);
        if(Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function create(array $data){
        if(isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            $data['thumbnail'] = $this->uploadThumbnail($data['thumbnail']);
        }
        return $this->productRepo->store($data);
    }

    public function update(int $id, array $data) {
        $fields = ['*'];
        $product = $this->productRepo->getById($id, $fields);
        if(isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            if($product->thumbnail) {
                $this->deleteThumbnail($product->thumbnail);
            }
            $data['thumbnail'] = $this->uploadThumbnail($data['thumbnail']);
        }
        return $this->productRepo->update($id, $data);
    }

    public function delete(int $id) {
        $fields = ['*'];
        $product = $this->productRepo->getById($id, $fields);
        if($product->thumbnail) {
            $this->deleteThumbnail($product->thumbnail);
        }
        $this->productRepo->delete($id);
    }
}