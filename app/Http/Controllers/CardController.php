<?php

namespace App\Http\Controllers;

use App\Enums\Card\StatusEnum;
use App\Http\Requests\Card\FreezeRequest;
use App\Http\Requests\Card\GetRequest;
use App\Http\Requests\Card\IssueRequest;
use App\Http\Requests\Card\ReissueRequest;
use App\Http\Requests\Card\SearchRequest;
use App\Http\Requests\Card\UnfreezeRequest;
use App\Http\Resources\Card\CardCollectionResource;
use App\Http\Resources\Card\CardResource;
use App\Services\CardService;
use Illuminate\Http\Response;

class CardController extends Controller
{
    public function __construct(
        protected CardService $cardService
    ) {
    }

    public function issue(IssueRequest $request): CardResource
    {
        $cardName = $request->validated('name', config('defaults.card_name'));

        $userId = $request->user()->id;

        $result = $this->cardService->issueCard($userId, $cardName);

        return CardResource::make($result);
    }

    public function get(GetRequest $request, int $id): CardResource
    {
        $card = $this->cardService->find($id);

        return CardResource::make($card);
    }

    public function search(SearchRequest $request): CardCollectionResource
    {
        $filters = $request->validated();

        $filters['user_id'] = $request->user()->id;

        $cards = $this->cardService->search($filters);

        return new CardCollectionResource($cards);
    }

    public function freeze(FreezeRequest $request, int $id): Response
    {
        $this->cardService->update($id, ['status' => StatusEnum::Freeze]);

        return response('', Response::HTTP_OK);
    }

    public function unfreeze(UnfreezeRequest $request, int $id): Response
    {
        $this->cardService->update($id, ['status' => StatusEnum::Active]);

        return response('', Response::HTTP_OK);
    }

    public function reissue(ReissueRequest $request, int $id): Response
    {
        $this->cardService->reissue($id);

        return response('', Response::HTTP_OK);
    }
}
