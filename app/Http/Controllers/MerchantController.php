<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantRequest;
use App\Http\Resources\MerchantResource;
use App\Services\MerchantService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    private $merchant;
    public function __construct(MerchantService $merchant) {
        $this->merchant = $merchant;
    }

    public function index() 
    {
        $fields = ['*'];
        $merchants = $this->merchant->getAll($fields);
        return response()->json(MerchantResource::collection($merchants));
    }

    public function show(int $id) {
        try {
            $fields = ['id','name','photo','keeper_id'];
            $merchant = $this->merchant->getById($id,$fields);
            return response()->json(new MerchantResource($merchant));
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'merchant not found',
            ],404);
        }
    }

    public function store(MerchantRequest $request) {
        try {
            $merchant = $this->merchant->create($request->validated());
            return response()->json(new MerchantResource($merchant));
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'failed to create merchant',
            ],500);
        }
    }

    public function update(MerchantRequest $request, int $id) {
        try {
            $merchant = $this->merchant->update($id, $request->validated());
            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message'=> 'merchant not found',
            ],404);
        }
    }

    public function destroy(int $id) {
        try {
            $this->merchant->delete($id);
            return response()->json([
                'message'=> 'merchant deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message'=> 'merchant not found',
            ],404);
        }
    }

    public function getByKeeperId() {
        $keeperId = Auth::id();
        if(!$keeperId) {
            return response()->json([
                'message'=> 'No auth available',
            ],404);
        }
        try {
            $merchant = $this->merchant->getByKeeperId($keeperId);
            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message'=> 'merchant not found for this user',
            ],404);
        }
    }
}
