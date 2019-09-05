<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SalmonScheduleRepository;

class SalmonScheduleController extends Controller
{
    private $repository;

    public function __construct(SalmonScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $isRequestingResults = $request->route()->getName() === 'schedules.results';
        return $this->repository->get($request->schedule_id, $isRequestingResults);
    }
}
