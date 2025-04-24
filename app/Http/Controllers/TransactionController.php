<?php

namespace App\Http\Controllers;

use Illuminate\Http\{
    Request,
    Response
};
use Illuminate\Support\Facades\Http;
use App\Models\{
    Transaction
};
use App\Services\Transaction\TransactionService;

class TransactionController extends Controller
{
    /**
     * @var TransactionService
     */
    protected $transactionService;

    /**
     * TransactionController constructor
     * @param TransactionService $transactionService
     */
    public function __construct (TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * TransactionController index
     * @param  Request $request
     * @return Response
     */
    public function index (Request $request): Response
    {
        return $this->transactionService->index($request);
    }

    /**
     * TransactionController show
     * @param  Transaction $transaction
     * @param  Request $request
     * @return Response
     */
    public function show (Transaction $transaction, Request $request): Response
    {
        return $this->transactionService->show($transaction, $request);
    }

    /**
     * TransactionController update
     * @param  Transaction $transaction
     * @param  Request $request
     * @return Response
     */
    public function update (Transaction $transaction, Request $request): Response
    {
        return $this->transactionService->update($transaction, $request);
    }
}
