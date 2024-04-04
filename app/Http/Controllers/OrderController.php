<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function order(Request $request){
        $uniqueId = uniqid();
        $order = new Order();
        $order->order_id = $uniqueId;
        $order->product_name = $request->product_name;
        $order->qty = $request->qty;
        $order->save();
        return view('orderImage', compact('uniqueId'));

    }
    public function uploadChunk(Request $request , $id)
    {
        $file = $request->file('file');
        $chunkIndex = $request->input('dzchunkindex');
        $chunkTotal = $request->input('dztotalchunkcount');

        $tempDirectory = "temp/{$file->getClientOriginalName()}";

        $file->storeAs($tempDirectory, "{$chunkIndex}");

        if ($chunkIndex == $chunkTotal - 1) {
            $filePath = "uploads/{$id}.{$file->getClientOriginalExtension()}";
            Storage::disk('local')->move("{$tempDirectory}", $filePath);
            $fileModel = new File();
            $fileModel->unique_id = $id;
            $fileModel->filename = $file->getClientOriginalName();
            $fileModel->path = $filePath;
            $fileModel->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true]);
    }
}
