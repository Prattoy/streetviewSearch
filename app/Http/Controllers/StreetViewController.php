<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Streetview;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
class StreetViewController extends Controller
{
    //
    public function UploadData(Request $request)
    {
      $lon = $request->longitude;
      $lat = $request->latitude;
      $imgLink = $request->imageLink;
      $geo_id = $request->geometry_id;

      for($i=0;$i<sizeof($lon);$i++)
      {
        // $x = new Streetview;
        // $x->longitude = $request->longitude;
        // $x->latitude = $request->latitude;
        // $x->imageLink = $this->saveFile($request->imageLink);
        // $x->geometry_id = $request->geometry_id;
        // $x->save();

        $x = new Streetview;
        $x->longitude = $lon[$i];
        $x->latitude = $lat[$i];
        $x->imageLink = $this->saveFile($imgLink[$i]);
        $x->geometry_id = $geo_id[$i];
        $x->save();

      }

      return response()->json(['Message' => 'Inserted']);

    }
    public function ShowData(Request $request)
    {

    }
    public function saveFile($file)
      {
        $filename = str_replace(' ', '_', $file->getClientOriginalName());
        Storage::put('public/'.$filename,  File::get($file));
        return $filename;
      }
      public function deleteFile($name)
      {
        Storage::delete($name);
        return response()->json('success');
      }
      public function getFileList(){
        $files = Storage::files('/public');
        $fileList = str_replace('public/', '', $files);
        return response()->json($fileList);
      }
      public function viewFile($id){

        $getData = Streetview::where('id','=',$id)->select('imageLink')->first();
        $name = $getData->imageLink;

        $path = storage_path('app/public/'.$name);

        //return response()->view($path);
         return response()->file($path);
        //return response()->json($path);
      }
}
