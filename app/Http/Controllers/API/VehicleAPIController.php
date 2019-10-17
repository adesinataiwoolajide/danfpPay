<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VehicleRepository;
use DB;
use App\{VehicleOwner, Vehicle, VehicleType, User, VehicleOperator};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
class VehicleAPIController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->hasRole('Owner')){

            $user = User::where('user_id', Auth::user()->user_id)->first();
            $own = VehicleOwner::where('email', Auth::user()->email)->first();
            $type = VehicleType::orderBy('type_name', 'asc')->get();
            $car = Vehicle::where('owner_id', $own->owner_id)->orderBy('vehicle_id', 'desc')->get();
            // return view('administrator.vehicles.index')->with([
            //     "own" => $own,
            //     'car' => $car,
            //     'type' => $type,
            // ]);
            return response()->json([
                'success' => true,
                'message' => "List of Vehicles",
                'data' => [
                    "own" => $own,
                    'car' => $car,
                    'type' => $type,
                ],
            ], 200);
        }elseif(auth()->user()->hasRole('Operator')){

            $user = User::where('user_id', Auth::user()->email)->first();
            $operator = VehicleOperator::where('email', Auth::user()->email)->first();
            $car = Vehicle::where('vehicle_id', $operator->vehicle_id)->orderBy('vehicle_id', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => "List of Vehicles",
                'data' => [
                    "operator" => $operator,
                    'user' => $user,
                    'car' => $car,

                ],
            ], 200);

        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($owner_number)
    {
        if(auth()->user()->hasPermissionTo('Add Vehicle') OR (Gate::allows('Administrator', auth()->user()))){
            $details = VehicleOwner::where('owner_number', $owner_number)->first();
            $owner_id = $details->owner_id;
            $car = Vehicle::where('owner_id', $owner_id)->get();
            $type = VehicleType::orderBy('type_name', 'asc')->get();
            // return view('administrator.vehicles.create')->with([
            //     'details' => $details,
            //     'type' => $type,
            //     'car' => $car
            // ]);
            return response()->json([
                'error' => true,
                'message' => '',
                'data' => [
                    'details' => $details,
                    'type' => $type,
                    'car' => $car
                ],
            ], 400);
        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->hasPermissionTo('Add Vehicle')){
            $this->validate($request, [
                'plate_number' =>'required|min:1|max:10',
                'brand' =>'required|min:1|max:255',
                'type_id' =>'required|min:1|max:255',
            ]);

            if(Vehicle::where("plate_number", $request->input("plate_number"))->exists()){
                //return redirect()->back()->with("error", "The Plate Number is in use by Another Vehicle");
                return response()->json([
                    'error' => true,
                    'message' => 'The Plate Number is in use by Another Vehicle',
                    'data' => [],
                ], 400);
            }

            $details = VehicleOwner::where('owner_id', $request->input("owner_id"))->first();

            $data = ([
                "vehicle" => new Vehicle,
                "plate_number" => $request->input("plate_number"),
                "brand" => $request->input("brand"),
                "type_id" => $request->input("type_id"),
                "owner_id" => $request->input("owner_id"),
                "vehicle_number" => $this->generateRandomHash(6),
            ]);

            if($this->model->create($data)){
                //return redirect()->back()->with("success", "You Have Added "
                // .$request->input("plate_number"). " To The Vehicle List Successfully");
                return response()->json([
                    'success' => true,
                    'message' => "You Have Added " .$request->input("plate_number"). " To The Vehicle List Successfully",
                    'data' => [],
                ], 200);
            }
        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($vehicle_id)
    {
        if(auth()->user()->hasPermissionTo('Edit Vehicle')){

            $vehicle = Vehicle::where('vehicle_id', $vehicle_id)->first();
            $owner_id = $vehicle->owner_id;
            $owner = VehicleOwner::where('owner_id', $vehicle->owner_id)->first();
            $type = VehicleType::orderBy('type_name', 'asc')->get();
            $car = Vehicle::orderBy('vehicle_id', 'desc')->get();
            return view('administrator.vehicles.edit')->with([
                'type' => $type,
                'vehicle' => $vehicle,
                'owner' => $owner,
                'car' => $car,
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Edit Vehicle',
                'data' => [
                    'type' => $type,
                    'vehicle' => $vehicle,
                    'owner' => $owner,
                    'car' => $car,
                ],
            ], 200);
        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $vehicle_id)
    {
        if(auth()->user()->hasPermissionTo('Update Vehicle') OR (Gate::allows('Administrator', auth()->user()))){
            $this->validate($request, [
                'plate_number' =>'required|min:1|max:10',
                'brand' =>'required|min:1|max:255',
                'type_id' =>'required|min:1|max:255',
            ]);

            $details = VehicleOwner::where('owner_number', $request->input("owner_id"))->first();
            $car = Vehicle::where('vehicle_id', $request->input("vehicle_id"))->first();

            if(empty($car->vehicle_number)){
                $vehicle_number = $this->generateRandomHash(6);
            }else{
                $vehicle_number = $car->vehicle_number;
            }

            $data = ([
                "vehicle" => $this->model->show($vehicle_id),
                "plate_number" => $request->input("plate_number"),
                "brand" => $request->input("brand"),
                "type_id" => $request->input("type_id"),
                "owner_id" => $request->input("owner_id"),
                "vehicle_number" => $vehicle_number,

            ]);

            if($this->model->update($data, $vehicle_id)){
                //return redirect()->route("vehicle.index")->with("success", "You Have Updated The Vehicle Successfully");
                return response()->json([
                    'success' => true,
                    'message' => 'You Have Updated The Vehicle Successfully',
                    'data' => [],
                ], 200);
            }
        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($vehicle_id)
    {
        if(auth()->user()->hasPermissionTo('Delete Vehicle') OR
            (Gate::allows('Administrator', auth()->user()))){
            $vehicle =  $this->model->show($vehicle_id);

            if (($vehicle->delete($vehicle_id))AND ($vehicle->trashed())) {
                // return redirect()->back()->with([
                //     'success' => "You Have Deleted The Vehicle with the plate number $vehicle->plate_number Successfully",
                // ]);

                return response()->json([
                    'success' => true,
                    'message' => 'You Have Deleted The Vehicle with the plate number $vehicle->plate_number Successfully',
                    'data' => [],
                ], 200);
            }
        } else{
            return response()->json([
                'error' => true,
                'message' => 'You dont have access to this page',
                'data' => [],
            ], 400);
        }
    }
}
