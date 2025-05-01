<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoverCommandRequest;
use App\Services\Rover\RoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoverController extends Controller
{

    public function __construct(
        readonly private RoverService $roverService,
    )
    {
    }

    public function execute(RoverCommandRequest $request): JsonResponse
    {
        $position = $request->input('position');
        $direction = $request->input('direction');
        $commands = $request->input('commands', '');
        $obstacles = $request->input('obstacles', []);

        $result = $this->roverService->executeCommands($position, $direction, $commands, $obstacles);

        return response()->json($result->toArray(), Response::HTTP_OK);
    }
}
