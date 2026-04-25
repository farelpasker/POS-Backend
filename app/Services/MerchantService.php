<?php

namespace App\Services;

use App\Repositories\MerchantRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class MerchantService
{
    private $repo;
    public function __construct(MerchantRepository $repo) {
        $this->repo = $repo;
    }

    public function getAll(array $fields) {
        return $this->repo->getAll($fields);
    }

    public function getById($id, array $fields) {
        return $this->repo->getById($id, $fields ?? ['id,name']);
    }

    public function create(array $data) {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadImage($data['photo']);
        }

        return $this->repo->create($data);
    } 

    public function update( $id ,array $data) {
        $fields = ['*'];
        $merchant = $this->repo->getById($id, $fields);
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if(!empty($merchant->photo)) {
                $this->deletePhoto($merchant->photo);
            }
            $data['photo'] = $this->uploadImage($data['photo']);
        }
        return $this->repo->update($id, $data);
    }

    public function delete($id) {
        $fields = ['*'];
        $merchant = $this->repo->getById($id, $fields);

        if($merchant->photo) {
            $this->deletePhoto($merchant->photo);
        }

        return $this->repo->delete($id); 
    }

    public function getByKeeperId(int $keeperId) {
        $fields = ['*'];
        return $this->repo->getKeeperId($keeperId, $fields);
    }

    public function uploadImage(UploadedFile $photo) {
        return $photo->store('merchants','public');
    }

    public function deletePhoto(string $photoPath) {
        $relativePath = 'warehouses/'. basename($photoPath);
        if(Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
    }
}