<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Streetview;
use App\GeoRoad;
use App\Deleted;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
class StreetViewController extends Controller
{
    // public static $geo_id;
    // public static $url;
    public function UploadData(Request $request)
    {
      $lon = $request->longitude;
      $lat = $request->latitude;
      $imgLink = $request->imageLink;
      $geo_id = $request->geometry_id;

      $path = "public/streetview/".$geo_id[0];
      // $path = "public/".$geo_id;
      if(!Storage::exists($path))
      {
        Storage::makeDirectory($path);
      }



      for($i=0;$i<count($lon);$i++)
      {
        // $x = new Streetview;
        // $x->longitude = $request->longitude;
        // $x->latitude = $request->latitude;
        // $x->imageLink = $this->saveFile($request->imageLink,$geo_id);
        // $x->geometry_id = $request->geometry_id;
        // $x->url = self::$url;
        // $x->save();

        $x = new Streetview;
        $x->longitude = $lon[$i];
        $x->latitude = $lat[$i];
        $x->imageLink = $this->saveFile($imgLink[$i],$geo_id[$i]);
        $x->geometry_id = $geo_id[$i];
        // $x->url = self::$url;
        $x->save();

      }

      if(count(GeoRoad::where('geometry_id','=',$geo_id[0])->select('geometry_id')->get())==0)
      {
        $road = new GeoRoad;
        $road->geometry_id = $geo_id[0];
        // $road->geometry_id = $geo_id;
        $road->road_name = $request->road_name[0];
        // $road->road_name = $request->road_name;
        $road->save();
      }
      return response()->json(['Message' => 'Inserted']);

    }
    public function ShowData(Request $request)
    {

    }
    public function saveFile($file,$geo_id)
      {
        $filename = str_replace(' ', '_', $file->getClientOriginalName());
        Storage::put('public/streetview/'.$geo_id.'/'.$filename,  File::get($file));
        // self::$url = storage_path('app/public/'.$geo_id.'/'.$filename);
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

        $getData = Streetview::where('id','=',$id)->select('geometry_id','imageLink')->first();
        $name = $getData->imageLink;
        $geo_id = $getData->geometry_id;

        $path = storage_path('app/public/streetview/'.$geo_id.'/'.$name);

        //return response()->view($path);
         return response()->file($path);
        // return response()->file($name);
      }

      public function viewStreet($geo_id)
      {
        $getData = Streetview::where('geometry_id','=',$geo_id)->select('id','longitude','latitude')->get();
        $getRoad = GeoRoad::where('geometry_id','=',$geo_id)->select('road_name')->first();
        $road_name = $getRoad->road_name;

        return response()->json(['name'=>$road_name,
          'geometry_id'=>$geo_id,
          'data'=>$getData]);
      }
      public function viewAll()
      {
        $Datas = Streetview::select('id','longitude','latitude','geometry_id')
        ->get()
        ->groupBy('geometry_id');

        $i = 0;
        foreach ($Datas as $Data)
        {
          foreach ($Data as $Dat)
          {
              $geo_id = $Dat->geometry_id;
              unset($Dat->geometry_id);
              // unset($Dat->url);

          }
          $getRoad = GeoRoad::where('geometry_id','=',$geo_id)->select('road_name')->first();
          $road_name = $getRoad->road_name;
          $files[$i] = array(
                'name'=>$road_name,
                'geometry_id' => $geo_id,
                'data' => $Data
              );
              $i++;
        }

        return response()->json($files);
      }

      public function geoSearch(Request $request)
      {
        $lat = $request->latitude;
        $lon = $request->longitude;
                 // $distance = 0.1;
                 // //$result = DB::select(“SELECT id, slc($lat, $lon, y(location), x(location))*10000 AS distance_in_meters, Address,area,longitude,latitude,pType,subType, astext(location) FROM places_2 WHERE MBRContains(envelope(linestring(point(($lat+(0.2/111)), ($lon+(0.2/111))), point(($lat-(0.2/111)),( $lon-(0.2/111))))), location) order by distance_in_meters LIMIT 1”);
                 // $result = DB::select(“SELECT id, ST_Distance_Sphere(Point($lon,$lat), location) as distance_in_meters,longitude,latitude,pType,Address,area,city,subType,uCode, contact_person_phone,ST_AsText(location)
                 // FROM places
                 // WHERE ST_Contains( ST_MakeEnvelope(
                 //   Point(($lon+($distance/111)), ($lat+($distance/111))),
                 //   Point(($lon-($distance/111)), ($lat-($distance/111)))
                 // ), location )
                 // ORDER BY distance_in_meters LIMIT 1");

        $distance = 0.1;
        $result = DB::select("SELECT id,ST_Distance_Sphere(Point($lon,$lat), location) as distance_in_meters,longitude,latitude,geometry_id,ST_AsText(location)
        FROM streetviews
        WHERE ST_Contains( ST_MakeEnvelope(
            Point(($lon+($distance/111)), ($lat+($distance/111))),
            Point(($lon-($distance/111)), ($lat-($distance/111)))
        ),location )
        ORDER BY distance_in_meters LIMIT 1");
        $id =$result[0]->id;
        $getData = Streetview::where('id','=',$id)->select('geometry_id','imageLink')->first();
        $name = $getData->imageLink;
        $geo_id = $getData->geometry_id;

        $path = storage_path('app/public/streetview/'.$geo_id.'/'.$name);

        return response()->file($path);

      }

      public function update(Request $request)
      {
        $id = $request->id;
        $imgLink = $request->imageLink;

        $image = Streetview::where('id','=',$id)->select('geometry_id','imageLink')->first();

        $oldPath = "public/streetview/".$image->geometry_id.'/'.$image->imageLink;
        $newPath = "public/deleted/".$image->geometry_id.'/'.$image->imageLink;
        $path = "public/deleted/".$image->geometry_id;
        // $path = "public/".$geo_id;

        //if folder exists
        if(!Storage::exists($path))
        {
          Storage::makeDirectory($path);
        }

        //move to new folder
        Storage::move( $oldPath, $newPath);

        $filename = str_replace(' ', '_', $imgLink->getClientOriginalName());
        Storage::put('public/streetview/'.$image->geometry_id.'/'.$filename,  File::get($imgLink));

        // $image->imageLink = $imgLink;
        // $image->save();
        Streetview::where('id','=',$id)
          ->update(['imageLink' => $filename]);

        $delete = new Deleted;
        $delete->imageLink = $image->imageLink;
        $delete->geometry_id = $image->geometry_id;
        $delete->save();

        return response()->json(['Message' => 'Updated']);
      }
}
