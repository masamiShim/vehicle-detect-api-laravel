<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function post(Request $request): string
    {
        try {
            \Illuminate\Support\Facades\Log::debug($request);
            $data = $request->all(['api_key', 'place_id', 'record_date']);
            $placeId = $data['place_id'];
            $recordDate = $data['record_date'];
            $path = "/{$placeId}/{$recordDate}";
            $disk = Storage::disk('plate');
            if (!$disk->exists($path)) {
                $disk->makeDirectory($path);
            }
            foreach ($request->files as $k => $file) {
                \Illuminate\Support\Facades\Log::debug($k);
                $f = $request->file($k);
                \Illuminate\Support\Facades\Log::debug($disk->path($path));
                $stored = $f->storeAs('plate'.$path, $f->getClientOriginalName());
                \Illuminate\Support\Facades\Log::debug($stored);
            }
            $disk->put($path.'/end.txt', 'end');
            return 'success';
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            Log::debug($exception->getTraceAsString());
            return 'failed';
        }
    }
}
