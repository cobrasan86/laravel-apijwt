<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\ComponentHttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
            ->product()
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only('name','sku','price','quantity');
        $validator = Validator::make($data,[
            'name' => 'required|string',
            'sku' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()],200);
        }

        $product = $this->user->product()->create([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ],Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = $this->user->product()->find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorrymproduct not found.'
            ],400);
        }
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->only('name','sku','price','quantity');
        $validator = Validator::make($data,[
            'name' => 'required|string',
            'sku' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()],200);
        }

        $product = $product->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product update successfully',
            'data' => $product
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ],Response::HTTP_OK);
    }
}
