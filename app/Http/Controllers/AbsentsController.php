<?php

namespace App\Http\Controllers;

use App\Models\Absent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AbsentsController extends Controller
{
    public function index(Request $request)
    {

        if (Auth::user()->role === 'admin') {
            $absents = Absent::OrderBy("id", "DESC")->paginate(2)->toArray();
        } else {
            $absents = Absent::where(['user_id'=> Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        }

        $response = [
            "total_count" => $absents["total"],
            "limit" => $absents["per_page"],
            "pagination" =>[
                "next_page" => $absents["next_page_url"],
                "current_page"=> $absents["current_page"]
            ],
            "data" => $absents["data"],
        ];
        
        return response()->json($response, 200);
    
    }
    public function store(Request $request)
    {

        if ($request->hasFile('foto')) {

            $imagName = Auth::user()->id . '_' . Carbon::now()->timestamp . '.' . $request->file('foto')->extension();
            $request->file('foto')->move(storage_path('uploads/images'), $imagName);

            $input = [
                'user_id' => Auth::user()->id,
                'lokasi' => $request->input('lokasi'),
                'foto' => $imagName,
                'tgl_absen' => Carbon::now()
            ];
    
            $validationRules =[
                'lokasi' => 'required'
            ];
            $validator = \Validator::make($input, $validationRules);
            if ($validator->fails()){
                return response()->json($validator->errors(), 400);
            }

            $absent = Absent::create($input);

            return response()->json($absent, 200);

        } else {
            return response()->json([
                'succes' => false,
                'status' => 404,
                'message' => 'field foto required'
            ], 404);
        }

    }


    public function show(Request $request, $id)
    {
        $absent = Absent::find($id);

        if(!$absent){
            $message = ['status' => 'error', 'message' => 'Data dengan ID '. $id . ' tidak ada'];

            return response()->json($message, 404); 
        }
        return response()->json($absent, 200);
    }

    public function update($id, Request $request)
    {
        $absent = Absent::find($id);
            
        if(!$absent){
            $message = ['status' => 'error', 'message' => 'Data dengan ID '. $id . ' tidak ada'];

            return response()->json($message, 404);    
        }

        if (Auth::user()->role === 'admin') {
            if ($request->hasFile('foto')) {

                $imagName = Auth::user()->id . '_' . Carbon::now()->timestamp . '.' . $request->file('foto')->extension();
                $request->file('foto')->move(storage_path('uploads/images'), $imagName);
    
                $input = [
                    'lokasi' => $request->input('lokasi'),
                    'foto' => $imagName,
                    'tgl_absen' =>  $request->input('tgl_absen')
                ];
        
                $validationRules =[
                    'lokasi' => 'required',
                    'tgl_absen' => 'required'
                ];
                $validator = \Validator::make($input, $validationRules);
                if ($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }
    
                $absent->fill($input);
                $absent->save();
    
                return response()->json($absent,200);
    
            } else {
                return response()->json([
                    'succes' => false,
                    'status' => 404,
                    'message' => 'field foto required'
                ], 404);
            }
        } else {
            $message = ['status' => 'error', 'message' => 'Hanya admin yang bisa mengupdate data'];

            return response()->json($message, 403);
        }


    }

    public function destroy(Request $request, $id)
    {
        if (Auth::user()->role === 'admin') {
            $absent = Absent::find($id);

            if(!$absent){
                $message = ['status' => 'error', 'message' => 'Data dengan ID '. $id . ' tidak ada'];

                return response()->json($message, 404); 
            }
            $absent->delete();
            $message = ['message' => 'deleted successfully', 'absent_id' => $id];

            return response()->json($message, 200);
        } else {
            $message = ['status' => 'error', 'message' => 'Hanya admin yang bisa menghapus data'];

            return response()->json($message, 403);
        }

    }



    public function image($imageName)
    {
        $imagePath = storage_path('uploads/images') . '/' . $imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }
}