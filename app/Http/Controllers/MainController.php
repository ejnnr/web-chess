<?php namespace App\Http\Controllers;



class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('main');
    }
}
