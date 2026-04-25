<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService {
    private $userRepo;
    public function __construct(UserRepository $userRepo) {
        $this->userRepo = $userRepo;
    }
    public function create(array $data) {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        $data['password'] = bcrypt($data['password']);
        return $this->userRepo->store($data);
    }

    public function update(int $id, array $data) {
        $user = $this->userRepo->findById($id);
        
        if(!$user) {
            throw ValidationException::withMessages([
                'user'=> ['User not found'],
            ]);
        }
        
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if($user->photo) {
                $this->deletePhoto($user->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->userRepo->update($data, $id);
    }

    public function delete(int $id) {
        $user = $this->userRepo->findById($id);
        if(!$user) {
            throw ValidationException::withMessages([
                'user'=> ['User not found'],
            ]);
        }

        if($user->photo) {
            $this->deletePhoto($user->photo);
        }
        return $this->userRepo->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo) {
        return $photo->store('users', 'public');
    }

    private function deletePhoto(string $photoPath) {
        $relativePath = 'users/' . basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

}