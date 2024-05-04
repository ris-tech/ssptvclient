<?php    
namespace App\Http\Controllers;
    
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
    
class LocationController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:location-list|location-create|location-edit|location-delete', ['only' => ['index','show']]);
         $this->middleware('permission:location-create', ['only' => ['create','store']]);
         $this->middleware('permission:location-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:location-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $locations = Location::latest()->paginate(5);
        return view('locations.index',compact('locations'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function all()
    {
        $locations = Location::get();
        return $locations;
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('locations.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        request()->validate([
            'name' => 'required',
            'street' => 'required',
            'streetno' => 'required',
            'postalcode' => 'required',
            'city' => 'required',
            'detail' => 'required',
        ]);
    
        Location::create($request->all());
    
        return redirect()->route('locations.index')
                        ->with('success','location created successfully.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location): View
    {
        return view('locations.show',compact('location'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location): View
    {
        return view('locations.edit',compact('location'));
    }
    
    /**
     * Show the tv builder.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edittv(Location $location): View
    {
        return view('locations.edittv',compact('location'));
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location): RedirectResponse
    {
         request()->validate([
            'name' => 'required',
            'street' => 'required',
            'streetno' => 'required',
            'postalcode' => 'required',
            'city' => 'required',
            'detail' => 'required',
        ]);
    
        $location->update($request->all());
    
        return redirect()->route('locations.index')
                        ->with('success','location updated successfully');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();
    
        return redirect()->route('locations.index')
                        ->with('success','location deleted successfully');
    }
}