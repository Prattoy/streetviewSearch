<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Streetview;
use App\StreetviewNew;
use App\GeoRoad;
use App\Deleted;
use App\linkHotSpots;
use App\initialViewParams;
use App\levels;
use App\defaultLinkHotspots;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Zipper;

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
        $x->location = DB::raw("ST_GEOMFROMTEXT('POINT($lon[$i] $lat[$i])')");
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

    public function saveData(Request $request)
    {
      $name = $request->road_name;
      $geo_id = $request->geometry_id;
      $scenes = $request->scenes;
      $defaultLinks = $request->defaultLinkHotspots;

      if(count(GeoRoad::where('geometry_id','=',$geo_id)->select('geometry_id')->get())==0)
      {
        $road = new GeoRoad;
        $road->geometry_id = $geo_id;
        $road->road_name = $name;
        $road->save();
      }

      foreach (json_decode($defaultLinks) as $defaultLink) 
      {
      	$deLink = new defaultLinkHotspots;
      	$deLink->geometry_id = $geo_id;
      	$deLink->yaw = $defaultLink->yaw;
      	$deLink->pitch = $defaultLink->pitch;
      	$deLink->rotation = $defaultLink->rotation;
      	$deLink->save();
      }

      foreach (json_decode($scenes) as $scene) 
      { 
        $lon = $scene->longitude;
        $lat = $scene->latitude;

        $street = new StreetviewNew;
        $street->geometry_id = $geo_id;
        $street->point_id = $scene->id;
        $street->longitude = $scene->longitude;
        $street->latitude = $scene->latitude;
        $street->location = DB::raw("ST_GEOMFROMTEXT('POINT($lon $lat)')");
        $street->faceSize = $scene->faceSize;
        $street->save();

        $street_id = StreetviewNew::where('point_id','=',$scene->id)->select('id')->first();
        $level_arr = $scene->levels;

        for($m = 0; $m < count($level_arr); $m++)
        {
        	if($m==0)
        	{
		        $level = new levels;
		        $level->street_id = $street_id->id;
		        $level->tileSize = $level_arr[$m]->tileSize;
		        $level->size = $level_arr[$m]->size;
		        $level->fallbackOnly = $level_arr[$m]->fallbackOnly;
		        $level->save();
        	}
        	else
        	{
        		$level = new levels;
		        $level->street_id = $street_id->id;
		        $level->tileSize = $level_arr[$m]->tileSize;
		        $level->size = $level_arr[$m]->size;
		        $level->save();
        	}
        }

        $initial_arr = $scene->initialViewParameters;

        $initial = new initialViewParams;
        $initial->street_id = $street_id->id;
        $initial->yaw = $initial_arr->yaw;
        $initial->pitch = $initial_arr->pitch;
        $initial->fov = $initial_arr->fov;
        $initial->save();

        $linkHotSpots = $scene->linkHotspots;
        if(count($linkHotSpots)>0)
        {
        	foreach ($linkHotSpots as $linkHotSpot) 
	        {
	          $link = new linkHotSpots;
	          $link->street_id = $street_id->id;
	          $link->yaw = $linkHotSpot->yaw;
	          $link->pitch = $linkHotSpot->pitch;
	          $link->rotation = $linkHotSpot->rotation;
	          $link->target = $linkHotSpot->target;
	          $link->save();
	        }
        }
        

        
      }

      if ($request->hasFile('zipFile')) 
      {
		// $zipFile = $request->file('zipFile');
	    $path = storage_path('app/public/streetview/'.$geo_id.'/');
	    \Zipper::make($request->file('zipFile'))->extractTo($path);
      }

      return response()->json(['Message' => 'Inserted']);
    }

    public function zipSaver(Request $request)
    {
      // $zipFile = $request->file('zipFile');
      // $zipFile = $request->zipFile;
      // $filename = str_replace(' ', '_', $zipFile->getClientOriginalName());
      // Storage::put('public/streetview/'.$filename, $zipFile);
      if ($request->hasFile('zipFile')) {

      	// $zipFile = $request->file('zipFile');
      	$path = storage_path('app/public/streetview/');
      	\Zipper::make($request->file('zipFile'))->extractTo($path);
        return response()->json(['Message' => 'Success']);
      }
      else
      {
        // \Zipper::make($zipFile)->extractTo('streetview');

      	return response()->json(['Message' => 'Zip file not found']);
      }      
    }

    public function saveImg(Request $request)
    {
      // $files = $request->imgFolder;
      // foreach ($files as $file) 
      // {
      //   // $img = $file->name;
      //   $this->images($file);
      // }
      
      
      return response()->json(['Message' => $request->imgFolder]);
    }
    public function images($file)
    {
      
      $filename = str_replace(' ', '_', $file->getClientOriginalName());
      Storage::put('public/streetview/array/'.$filename,  File::get($file));

    }

    // public function ShowData(Request $request)
    // {

    // }

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

    public function getFileList()
    {
        $files = Storage::files('/public/streetview');
        $fileList = str_replace('public/streetview', '', $files);
        return response()->json($fileList);
    }

    public function viewFile($id)
    {

        $getData = Streetview::where('id','=',$id)->select('geometry_id','imageLink')->first();
        $name = $getData->imageLink;
        $geo_id = $getData->geometry_id;

        $path = storage_path('app/public/streetview/'.$geo_id.'/'.$name);

        return response()->file($path);
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

        if(!empty($Datas))
        {
          $i = 0;
          foreach ($Datas as $Data)
          {
            foreach ($Data as $Dat)
            {
                $geo_id = $Dat->geometry_id;
                unset($Dat->geometry_id);
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
        else
        {
          return response()->json(['No Data Found']);
        }
    }

    public function viewAllNew()
      {
        $Datas = StreetviewNew::select('point_id','longitude','latitude','geometry_id')
        ->get()
        ->groupBy('geometry_id');

        $i = 0;
        foreach ($Datas as $Data) 
        {
          $new_data = null;
          $j = 0;
          foreach ($Data as $Dat) 
          {
              $new_data[$j] = array(
                'id'=>$Dat->point_id,
                'longitude' => $Dat->longitude,
                'latitude' => $Dat->latitude
              );
              $j++;

              $geo_id = $Dat->geometry_id;
              unset($Dat->geometry_id);
          }
          $getRoad = GeoRoad::where('geometry_id','=',$geo_id)->select('road_name')->first();
          if($getRoad->road_name!='')
          {
            $road_name = $getRoad->road_name;
          }
          
          $files[$i] = array(
                'name'=>$road_name,
                'geometry_id' => $geo_id,
                'data' => $new_data
              );
              $i++;
        }
        
        if(isset($files))
        {
        	return response()->json($files);
        }
        else
        {
        	return response()->json(['Message' => 'No Data Found']);
        }
        
      }

    public function geoSearch(Request $request)
    {
        $lat = $request->latitude;
        $lon = $request->longitude;

        $distance = 0.1;
        $result = DB::select("SELECT id,ST_Distance_Sphere(Point($lon,$lat), location) as distance_in_meters,longitude,latitude,geometry_id,ST_AsText(location)
        FROM streetviews
        WHERE ST_Contains( ST_MakeEnvelope(
            Point(($lon+($distance/111)), ($lat+($distance/111))),
            Point(($lon-($distance/111)), ($lat-($distance/111)))
        ),location )
        ORDER BY distance_in_meters LIMIT 1");
        if(array_key_exists('0', $result))
        {
          $id =$result[0]->id;
          $getData = Streetview::where('id','=',$id)->select('latitude','longitude','geometry_id')->first();

          return response()->json(['id' => $id,
                                'road_id' => $getData->geometry_id,
                                'latitude' => $getData->latitude,
                                'longitude' => $getData->longitude]);

        }
        else
        {
          return response()->json('No data found!');
        }
    }

    public function geoSearchNew(Request $request)
    {
    	if ($request->has('point_id')) 
    	{
    		$name_road = $request->point_id;
	    	$road = StreetviewNew::where('point_id','=',$name_road)->select('latitude','longitude')->first();
	        $lat = $road->latitude;
	        $lon = $road->longitude;
    	}
    	else
    	{
    		$lat = $request->latitude;
        	$lon = $request->longitude;
    	}
        

        $distance = 0.1;
        $result = DB::select("SELECT id,ST_Distance_Sphere(Point($lon,$lat), location) as distance_in_meters,longitude,latitude,point_id,geometry_id,ST_AsText(location)
        FROM streetviews_new
        WHERE ST_Contains( ST_MakeEnvelope(
            Point(($lon+($distance/111)), ($lat+($distance/111))),
            Point(($lon-($distance/111)), ($lat-($distance/111)))
        ),location )
        ORDER BY distance_in_meters LIMIT 1");
        if(array_key_exists('0', $result))
        {
          $id =$result[0]->id;
          $point_id = $result[0]->point_id;

          $getData = StreetviewNew::where('id','=',$id)->select('latitude','longitude','geometry_id')->first();
          $road_name = GeoRoad::where('geometry_id','=',$getData->geometry_id)->select('road_name')->first();
          $datas = StreetviewNew::where('geometry_id','=',$getData->geometry_id)->select('id','latitude','longitude','point_id','faceSize')->get();
          $defaultDatas = defaultLinkHotspots::where('geometry_id','=',$getData->geometry_id)->select('yaw','pitch','rotation')->get();          

          for($i = 0;$i<count($datas);$i++)
          {
            // $street_id = StreetviewNew::where('point_id','=',$datas[$i]->point_id)->select('id')
            $street_id = $datas[$i]->id;

            $levels = levels::where('street_id','=',$street_id)->select('tileSize','size','fallbackOnly')->get();

            for($k = 0; $k < count($levels); $k++)
            {
            	if($k!=0)
            	{
            		unset($levels[$k]->fallbackOnly);
            	}
            	else
            	{
            		if($levels[$k]->fallbackOnly==1)
            		{
            			$levels[$k]->fallbackOnly = true;
            		}
            		else
            		{
            			$levels[$k]->fallbackOnly = false;
            		}
            	}
            }

            $initialViewParameters = initialViewParams::where('street_id','=',$street_id)->select('yaw','pitch','fov')->first();

            $linkHotSpots = linkHotSpots::where('street_id','=',$street_id)->select('yaw','pitch','rotation','target')->get();

            $scenes[$i] = array(
                'id'=> $datas[$i]->point_id,
                'latitude' => $datas[$i]->latitude,
                'longitude' => $datas[$i]->longitude,
                'levels' => $levels,
                'faceSize' => $datas[$i]->faceSize,
                'initialViewParameters' => $initialViewParameters,
                'linkHotspots' => $linkHotSpots
              );
          }
          
          if(empty($defaultDatas))
          {
          	return response()->json(['geometry_id' => $getData->geometry_id,
          							'point_id' => $point_id,
                                  'road_name' => $road_name->road_name,
                                'latitude' => $getData->latitude,
                                'longitude' => $getData->longitude,
                                'scenes' => $scenes,
                              ]);
          }
          else
          {
          	return response()->json(['geometry_id' => $getData->geometry_id,
          							'point_id' => $point_id,
                                  	'road_name' => $road_name->road_name,
                                	'latitude' => $getData->latitude,
                                	'longitude' => $getData->longitude,
                                	'defaultLinkHotspots' => $defaultDatas,
                                	'scenes' => $scenes,
                              	]);
          }   

        }
        else
        {
          return response()->json('No data found!');
        }
    }

    /*public function geoSearchPoint(Request $request)
    {
    	$name_road = $request->point_id;
    	$road = StreetviewNew::where('point_id','=',$name_road)->select('latitude','longitude')->first();
        $lat = $road->latitude;
        $lon = $road->longitude;

        $distance = 0.1;
        $result = DB::select("SELECT id,ST_Distance_Sphere(Point($lon,$lat), location) as distance_in_meters,longitude,latitude,point_id,geometry_id,ST_AsText(location)
        FROM streetviews_new
        WHERE ST_Contains( ST_MakeEnvelope(
            Point(($lon+($distance/111)), ($lat+($distance/111))),
            Point(($lon-($distance/111)), ($lat-($distance/111)))
        ),location )
        ORDER BY distance_in_meters LIMIT 1");
        if(array_key_exists('0', $result))
        {
          $id =$result[0]->id;
          $point_id = $result[0]->point_id;

          $getData = StreetviewNew::where('id','=',$id)->select('latitude','longitude','geometry_id')->first();
          $road_name = GeoRoad::where('geometry_id','=',$getData->geometry_id)->select('road_name')->first();
          $datas = StreetviewNew::where('geometry_id','=',$getData->geometry_id)->select('id','latitude','longitude','point_id','faceSize')->get();

          for($i = 0;$i<count($datas);$i++)
          {
            // $street_id = StreetviewNew::where('point_id','=',$datas[$i]->point_id)->select('id')
            $street_id = $datas[$i]->id;

            $level_db = levels::where('street_id','=',$street_id)->select('tileSize1','size1','fallbackOnly','tileSize2','size2','tileSize3','size3')->first();
            
            if($level_db->fallbackOnly==1)
            {
            	$bool = true;
            }
            else
            {
            	$bool = false;
            }

            $levels[0] = array(
                'tileSize' => $level_db->tileSize1,
                'size' => $level_db->size1,
                'fallbackOnly' => $bool
              );

            $levels[1] = array(
            	'tileSize' => $level_db->tileSize2,
            	'size' => $level_db->size2
            );

            $levels[2] = array(
            	'tileSize' => $level_db->tileSize3,
            	'size' => $level_db->size3
            );

            $initialViewParameters_db = initialViewParams::where('street_id','=',$street_id)->select('yaw','pitch','fov')->first();

            $initialViewParameters = array(
            	'yaw' => $initialViewParameters_db->yaw,
            	'pitch' => $initialViewParameters_db->pitch,
            	'fov' => $initialViewParameters_db->fov,
            );

            $linkHotSpots_db = linkHotSpots::where('street_id','=',$street_id)->select('yaw','pitch','rotation','target')->get();

            for($c = 0;$c<count($linkHotSpots_db);$c++)
            {
            	$linkHotSpots[$c] = array(
            		'yaw' => $linkHotSpots_db[$c]->yaw,
            		'pitch' => $linkHotSpots_db[$c]->pitch,
            		'rotation' => $linkHotSpots_db[$c]->rotation,
            		'target' => $linkHotSpots_db[$c]->target
            	);
            }

            $scenes[$i] = array(
                'id'=> $datas[$i]->point_id,
                'latitude' => $datas[$i]->latitude,
                'longitude' => $datas[$i]->longitude,
                'levels' => $levels,
                'faceSize' => $datas[$i]->faceSize,
                'initialViewParameters' => $initialViewParameters,
                'linkHotspots' => $linkHotSpots
              );
          }
          

          return response()->json(['geometry_id' => $getData->geometry_id,
          							'point_id' => $point_id,
                                  'road_name' => $road_name->road_name,
                                'latitude' => $getData->latitude,
                                'longitude' => $getData->longitude,
                                'scenes' => $scenes
                              ]);

        }
        else
        {
          return response()->json('No data found!');
        }
    }*/

    public function update(Request $request)
    {
        $id = $request->id;
        $imgLink = $request->imageLink;

        $image = Streetview::where('id','=',$id)->select('geometry_id','imageLink')->first();

        $oldPath = "public/streetview/".$image->geometry_id.'/'.$image->imageLink;
        $newPath = "public/updated/".$image->geometry_id.'/'.$image->imageLink;
        $path = "public/updated/".$image->geometry_id;
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

    public function delete(Request $request)
    {
        $road_id = $request->road_id;

        $delInStreet = Streetview::where('geometry_id',$road_id)->delete();
        $delInGeo = GeoRoad::where('geometry_id',$road_id)->delete();

        $oldPath = "public/streetview/".$road_id;
        $newPath = "public/deleted/".$road_id;

        Storage::move( $oldPath, $newPath);

        return response()->json(['Message' => 'Deleted']);
    }

    public function deleteAll(Request $request)
    {
    	$geo_id = $request->geometry_id;

    	$street_id = StreetviewNew::where('geometry_id','=',$geo_id)->select('id')->first();

    	$delInStreetNew = StreetviewNew::where('geometry_id',$geo_id)->delete();
        $delInGeo = GeoRoad::where('geometry_id',$geo_id)->delete();
        $delIndefaultLink =defaultLinkHotspots::where('geometry_id',$geo_id)->delete();
        $delLink =linkHotSpots::where('street_id',$street_id->id)->delete();
        $delIni =initialViewParams::where('street_id',$street_id->id)->delete();
        $delLevels =initialViewParams::where('street_id',$street_id->id)->delete();

        return response()->json(['Message' => 'Deleted']);
    }

    public function folders()
    {
      $path = 'public/streetview/';
      // $files = Storage::Files($path);
      $directories = Storage::directories($path);
      $files = Storage::Files($path);

      $i=0;
      foreach ($directories as $directory) {
        $result[$i] = str_replace($path, '', $directory);
        $i++;
      }

      foreach ($files as $file) {
        $result[$i] = str_replace($path, '', $file);
        $i++;
      }
 
      return response()->json($result);
    }

    public function slug($slug = null)
    {
      $path = 'public/streetview/'.$slug;
      // $basename = basename($path);
      // $arr = explode('/',$path);
      // $realName = end($arr);

      // $arr = explode('/',$path);
      // $name = end($arr);
      

      $allDirectories = Storage::allDirectories('public/streetview/');
      $allFiles = Storage::allFiles('public/streetview/');
      // $i=0;
      // foreach ($allDirectories as $allDirectory) {
      //   $arr = explode('/',$allDirectory);
      //   $directories[$i] = end($arr);
      //   $i++;
      // }

      // if(in_array($realName, $directories))
      if(in_array($path, $allDirectories))
      {
        // $result = 'directory';
        $directories = Storage::directories($path);
        $files = Storage::Files($path);
        $results = array_merge($directories,$files);

        $i=0;
        foreach ($results as $result) {
          $arr = explode('/',$result);
          $names[$i] = end($arr);
          $i++;
        }

        if(empty($names))
        {
          return response()->json('The folder is empty');
        }
        else
        {
          return response()->json($names);
        }
        
      }
      else if(in_array($path, $allFiles))
      {
        $url = storage_path('app/'.$path);
        return response()->file($url);
      }
      else
      {
        return response()->json('Sorry, this is not a valid file path');
      }
      
    }

    public function test(Request $request)
    {
    	$id = StreetviewNew::where('geometry_id','=',1684)->select('id')->first();
    	$defaultDatas = levels::where('street_id',$id->id)->get();
         //  if(array_key_exists('0', $defaultDatas))
         //  {
         //  	for ($n = 0; $n<count($defaultDatas); $n++) 
	        // {
	        //   	$dlh[$n] = array(
	        //   		'yaw' => $defaultDatas[$n]->yaw,
	        //   		'pitch' => $defaultDatas[$n]->pitch,
	        //   		'rotation' => $defaultDatas[$n]->rotation
	        //   	);
	        // }
         //  }

          return response()->json($defaultDatas);
    }
}
