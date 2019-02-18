<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Streetview;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
class StreetViewController extends Controller
{
    // public static $geo_id;
    public static $url;
    public function UploadData(Request $request)
    {
      $lon = $request->longitude;
      $lat = $request->latitude;
      $imgLink = $request->imageLink;
      $geo_id = $request->geometry_id;

      $path = "public/".$geo_id[0];
      if(!Storage::exists($path))
      {
        Storage::makeDirectory($path);
      }
      


      for($i=0;$i<count($lon);$i++)
      {
        // $x = new Streetview;
        // $x->longitude = $request->longitude;
        // $x->latitude = $request->latitude;
        // $x->imageLink = $this->saveFile($request->imageLink);
        // $x->geometry_id = $request->geometry_id;
        // $x->url = self::$url;
        // $x->save();

        $x = new Streetview;
        $x->longitude = $lon[$i];
        $x->latitude = $lat[$i];
        $x->imageLink = $this->saveFile($imgLink[$i],$geo_id[$i]);
        $x->geometry_id = $geo_id[$i];
        $x->url = self::$url;
        $x->save();

      }

      return response()->json(['Message' => 'Inserted']);

    }
    public function ShowData(Request $request)
    {

    }
    public function saveFile($file,$geo_id)
      {
        $filename = str_replace(' ', '_', $file->getClientOriginalName());
        Storage::put('public/'.$geo_id.'/'.$filename,  File::get($file));
        self::$url = storage_path('app/public/'.$geo_id.'/'.$filename);
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

        $getData = Streetview::where('id','=',$id)->select('url')->first();
        // $name = $getData->imageLink;
        $name = $getData->url;

        // $path = storage_path('app/public/'.$name);

        //return response()->view($path);
         //return response()->file($path);
        return response()->file($name);
      }

      public function viewStreet($geo_id)
      {
        $getData = Streetview::where('geometry_id','=',$geo_id)->select('id','longitude','latitude','url')->get();

        return response()->json(['geometry_id'=>$geo_id,
          'data'=>$getData]);
      }
      public function viewAll()
      {
        $Datas = Streetview::select('id','longitude','latitude','url','geometry_id')
        ->get()
        ->groupBy('geometry_id');

        $i = 0;
        foreach ($Datas as $Data) 
        {
          foreach ($Data as $Dat) 
          {
              $geo = $Dat->geometry_id;
              unset($Dat->geometry_id);
              
          }
          $files[$i] = array(
                'geometry_id' => $geo,
                'data' => $Data
              );
              $i++;
        }
        
        return response()->json($files);
      }
}
