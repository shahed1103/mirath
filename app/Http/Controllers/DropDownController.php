<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\response;
use App\Services\DropDownService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class DropDownController extends Controller
{
    private DropDownService $dropDownService;

    public function __construct(DropDownService  $dropDownService){
        $this->dropDownService = $dropDownService;
    }

    public function getNationalities(): JsonResponse {
        $data = [] ;
        try{
            $data = $this->dropDownService->getNationalities();
            return Response::Success($data['nationalities'], $data['message']);
        }
        catch(Throwable $th){
            $message = $th->getMessage();
            $errors [] = $message;
            return Response::Error($data , $message , $errors);
        }
    }
}