<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    private $repo;
    private $service;

    public function __construct(TransactionRepository $repo, TransactionService $service) {
        $this->repo = $repo;
        $this->service = $service;
    }

    public function index() {
        $fields = ['*'];
        $transactions = $this->repo->getAll($fields);
        return response()->json([
            'status' => 'success',
            'message' => 'Transactions retrieved successfully',
            'data' => TransactionResource::collection($transactions)
        ]);
    }

    public function store(TransactionRequest $request) {
        DB::beginTransaction();
        try {
            $transaction = $this->service->createTransaction($request->validated());
            DB::commit();
            return response()->json([
                'status'=> 'success',
                'message'=> 'Success create Transaction',
                'data'=> new TransactionResource($transaction)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
            ], 500);
        }
    }

    public function show($id) {
        try {
            $transaction = $this->repo->getById($id,['*']);
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction retrieved successfully',
                'data' => new TransactionResource($transaction),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found',
                'data' => null,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTransactionByMerchant() {
        $user = auth()->user();

        if(!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No auth available'
            ],500);
        }

        if(!$user->merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'No merchant assigned'
            ],403);
        }

        $transactions = $this->repo->getTransactionsByMerchant($user->merchant->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ]);
    }
        
    
}
