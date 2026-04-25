<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AuthService {
    private $repo;

    public function __construct(AuthRepository $repo) {
        $this->repo = $repo;
    }

    public function register(array $data) {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->repo->register($data);
    }

    public function login(array $data) {
        return $this->repo->login($data);
    }

    public function tokenLogin(array $data) {
        return $this->repo->tokenLogin($data);
    }

    private function uploadPhoto(UploadedFile $photo) {
        return $photo->store('users', 'public');
    }

    private function removePhoto(string $photo) {
        $relativePath = 'users/'.$photo;
        if(Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}