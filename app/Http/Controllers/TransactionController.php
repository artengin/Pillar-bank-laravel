<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\SearchRequest;
use App\Http\Resources\Transaction\TransactionCollectionResource;
use App\Http\Requests\Transaction\HandleWebhookRequest;
use App\Http\Requests\Transaction\GetRequest;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use App\Http\Resources\Transaction\TransactionResource;
use App\Jobs\HandleTransactionWebhookJob;

class TransactionController extends Controller
{
    public function handleWebhook(HandleWebhookRequest $request): Response
    {
        HandleTransactionWebhookJob::dispatch($request->validated());

        return response('', Response::HTTP_OK);
    }

    public function search(SearchRequest $request, TransactionService $service): TransactionCollectionResource
    {
        $filter = $request->validated();

        $filter['user_id'] = $request->user()->id;

        $transactions = $service->search($filter);

        return TransactionCollectionResource::make($transactions);
    }

    public function get(GetRequest $request, TransactionService $service, int $id): TransactionResource
    {
        $transaction = $service->find($id);

        return TransactionResource::make($transaction);
    }
}
