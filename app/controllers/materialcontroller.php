<?php
use Intervention\Image\ImageManager;
class materialcontroller extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function __construct(User $user, Material $material,Categorie $categorie,Accessorie $accessorie, Reservation $reservation)
	{
		$this->user = $user;
		$this->material = $material;
		$this->categorie = $categorie;
		$this->accessorie = $accessorie;
		$this->reservation = $reservation;

	}

	public function index()
	{
		if(Auth::check())
		{
			$categories = $this->categorie->getCategoriesWhitMaterials();
			return View::make('users.index',['categories' => $categories]);
		}
		else
		{
			return Redirect::to('/');
		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Auth::check())
		{
			$categories = Categorie::getAllCategories();
			$accesoriesCategorie = $this->categorie->getCategoriesWhitMaterials(); // alle accessories an de hand van categorie
			return View::make('users.admin.addMaterial',['categories' => $categories,'accesoriesCategorie' => $accesoriesCategorie]);
		}
		else
		{
			return Redirect::to('/');
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(Auth::check())
		{	
			if( $this->material->fill(Input::all())->isValid('add'))
			{
				$filename = 'nofile.png';
				if(Input::hasFile('image'))
				{
				
					$filename = substr_replace(Input::file('image')->getClientOriginalName() ,"",-4).Input::get('name').'.png';
					$image = Image::make(Input::file('image')->getRealPath())->heighten(500);
					$image->crop(500,500);
					$destenation = 'images/'.$filename;
					$image->save($destenation);		
				}
				$this->material->status = 'ok';
				$this->material->image = $filename;
				$this->material->save();
				$this->categorie->saveMaterialToCategorie(Input::get('categorie'),$this->material->id);
				$this->accessorie->saveAccessories(Input::get('accessories'),$this->material->id);
				return Redirect::to('/materials/create')->with('message', 'U heeft succesvol '.Input::get('name').' toegevoegd aan de lijst van materiaal');
			}
			else
			{
				return Redirect::back()->withInput()->withErrors($this->material->errors);
			}
		}
		else{
			return Redirect::to('/');
		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if(Auth::check())
		{
			/*return $this->reservation->getMaterialStatus($id);*/
			$material = $this->material->getMaterialById($id);
			$cal = $this->configCal(date("Y-m-d H:i:s"),$id);
			return View::make('materials.detail',['material' => $material,'cal' => $cal]);
		}
		else
		{
			return Redirect::to('/');
		}
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if(Auth::check())
		{
			$categories = Categorie::getAllCategories();
			$accesoriesCategorie = $this->categorie->getCategoriesWhitMaterials();
			$material = Material::find($id);
			$accessoriesOfMaterial = $this->material->getMaterialAccessoriesArray($id);
			return View::make('users.admin.materialEdit',['material' => $material,'categories' => $categories,'accesoriesCategorie' => $accesoriesCategorie,'accessoriesOfMaterial' => $accessoriesOfMaterial]);
		}
		else
		{
			return Redirect::to('/');
		}
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if(Auth::check())
		{
			$this->material = Material::find($id);
			if( $this->material->fill(Input::all())->isValid('edit'))
			{
				if(Input::hasFile('image'))
				{
					$filename = substr_replace(Input::file('image')->getClientOriginalName() ,"",-4).Input::get('name').'.png';
					$image = Image::make(Input::file('image')->getRealPath())->heighten(500);
					$image->crop(500,500);
					$destenation = 'images/'.$filename;
					$image->save($destenation);		
				}
				else
				{
					$material = Material::select('image')->find($id);
					$filename = $material->image;
				}
				$this->material->image = $filename;
				$this->material->save();
				$this->categorie->updateMaterialCategorie(Input::get('categorie'),$id);
				$this->accessorie->updateAccessories(Input::get('accessories'),$this->material->id);
				return Redirect::to('/beheer/materiaal')->with('message', 'u hebt succesvol '.Input::get('name').' aangepast');
				
			}
			else
			{
				return Redirect::back()->withInput()->withErrors($this->material->errors);
			}
		}
		else
		{
			return Redirect::to('/');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if(Auth::check())
		{
			$name = Material::find($id)->name;
			$this->material->deleteMaterial($id);
			return Redirect::to('/beheer/materiaal')->with('message', 'u hebt succesvol '.$name.' verwijderd');
		}
		else
		{
			return Redirect::to('/');
		}
	}

	public function calNext($id)
	{
		$material = $this->material->getMaterialById($id);
		$cal = $this->configCal($_GET["cdate"],$id);	
		return View::make('materials.detail',['material' => $material,'cal' => $cal]);
	}

	public function configCal($date,$id)
	{
		$events = $this->reservation->getMaterialStatus($id);
	    $cal = Calendar::make();
	    $cal->setDate($date); //Set starting date
	    $cal->setBasePath('/materials/'.$id.'/cal'); // Base path for navigation URLs
	    $cal->showNav(true); // Show or hide navigation
	    $cal->setView("week"); //'day' or 'week' or null
	    $cal->setStartEndHours(8,22); // Set the hour range for day and week view
	    $cal->setTimeClass('ctime'); //Class Name for times column on day and week views
	    $cal->setEventsWrap(array('<p>', '</p>')); // Set the event's content wrapper
	    $cal->setDayWrap(array('<div>','</div>')); //Set the day's number wrapper
	    $cal->setNextIcon('Volgende Week'); //Can also be html: <i class='fa fa-chevron-right'></i>
	    $cal->setPrevIcon('Vorige Week'); // Same as above
	    $cal->setDayLabels(array('Zon', 'Man', 'Din', 'Woe', 'Don', 'Vrij', 'Zat')); //Label names for week days
	    $cal->setMonthLabels(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')); //Month names
	    $cal->setDateWrap(array('<div>','</div>')); //Set cell inner content wrapper
	    $cal->setTableClass('table'); //Set the table's class name
	    $cal->setHeadClass('table-header'); //Set top header's class name
	    $cal->setNextClass('btn'); // Set next btn class name
	    $cal->setPrevClass('btn'); // Set Prev btn class name
	    $cal->setEvents($events);
	    return $cal;
	}

	public function getLogbook()
	{
		Session::forget('input');
		$categories = Categorie::getAllCategories();
		$categories['all'] ='alle';
		$logbook = Material::paginate(8);	
		return View::make('materials.logbook',['logbook' =>$logbook,'paginate' => true,'categories' =>$categories]);
	}

	public function getReservations($id)
	{
		$reservations = $this->reservation->getLastReservations($id);
		return View::make('materials.lastReservation',['reservations' => $reservations]);
	}

	public function filterLogbook()
	{
		Session::forget('input');
		if((Input::get('search') == '') && (Input::get('status') == 'all') && (Input::get('availability') == 'all') && (Input::get('categorie') == 'all'))
		{
			return Redirect::to('/logbook');
		}
		else
		{
			$categories = Categorie::getAllCategories();
			$categories['all'] ='alle';
			$result = $this->material->searchMaterial(Input::get('search'),Input::get('status'),Input::get('availability'),Input::get('categorie'));
			Session::put('input',Input::all());
			return View::make('materials.logbook',['logbook' => $result,'paginate' => false,'categories' =>$categories]);	
		}
		
	}


}
