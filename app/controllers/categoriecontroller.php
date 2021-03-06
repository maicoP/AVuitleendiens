<?php

class categoriecontroller extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function __construct(Categorie $categorie)
	{

		$this->categorie = $categorie;

	}

	public function index()
	{
		//
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
			return View::make('users.admin.addCategorie');
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
			if( $this->categorie->fill(Input::all())->isValid())
			{
				$this->categorie->save();
				return Redirect::to('/categories/create')->with('message', 'u hebt succesvol '.Input::get('name').' toegevoegd aan de lijst van categorien');
			}
			else
			{
				return Redirect::back()->withInput()->withErrors($this->categorie->errors);
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
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

		return View::make('users.admin.editCategorie',['categorie' => Categorie::find($id)]);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$this->categorie = Categorie::find($id);
		if( $this->categorie->fill(Input::all())->isValid())
		{
			$this->categorie->save();
			return Redirect::to('/beheer/materiaal')->with('message', 'u hebt succesvol '.Input::get('name').' gewijzigt');
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($this->categorie->errors);
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
		$this->categorie = Categorie::find($id);
		if($this->categorie->isCategoryEmpty($id))
		{
			$this->categorie->delete(); 
			return Redirect::to('/beheer/materiaal')->with('message', 'u hebt de categorie '.$this->categorie->name.' met succes verwijderd');	
		}
		else
		{
			return Redirect::to('/beheer/materiaal')->with('message', 'u moet eerst alle materiaal uit '.$this->categorie->name.' verwijderen voor u deze categorie kan verwijderen');
		}
		
	}


}
