<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\{
    Request,
    Response
};

use App\Http\Requests\BoardRequest;
use App\Services\Leader\BoardService;


class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $boardService;

    public function __construct (BoardService $boardService)
    {
        $this->boardService = $boardService;
    }

    public function index (Request $request): Response
    {
        return $this->boardService->index($request);
    }

    public function store (BoardRequest $request): Response
    {
        return $this->boardService->store($request);
    }
  
    public function show (Board $board, Request $request): Response
    {
        return $this->boardService->show($board, $request);
    }
    
    public function update (Board $board, BoardRequest $request): Response
    {
        return $this->boardService->update($board, $request);
    }
    
    public function destroy (Board $board, Request $request): Response
    {
        return $this->boardService->destroy($board, $request);
    }
}