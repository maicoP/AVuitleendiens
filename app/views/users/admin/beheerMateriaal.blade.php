@extends("global.base")

@section("page-title")
	Overzicht
@stop

@section("nav")
	@include("global.nav")
@stop

@section("content")
	<span><a href="/beheer">Beheer </a>> Materiaal</span>
	<h2>Beheer Materiaal</h2>
	<div>
		@if(Session::has('message'))
	        <div>{{ Session::get('message')}}</div>
	    @endif
	</div>
	<div class="loginbox">    
		<a href="/materials/create">
		    <button class="btn btnreg btn-success btn-default">Materiaal toevoegen</button>
		</a>
		<a href="/categories/create">
		    <button class="btn btnreg btn-success btn-default">Categorie toevoegen</button>
		</a>
	</div>
	<div>
		@forelse($categories as $categorie)

			<div class="category">
				
				<div class="inline">
					<br>{{{$categorie->name}}}
					{{ Form::open(['route' => ['categories.edit', $categorie->id], 'method' => 'GET', 'class' => 'inline']) }}
					<input class="editbutton" height="32" width="32" type="image" src="../../assets/images/edit.png" alt="EDIT">
					{{ Form::close() }}
					{{ Form::open(['route' => ['categories.destroy', $categorie->id], 'method' => 'delete','class'=>'inline']) }}
					<input class="editbutton editspace" height="32" width="32"  type="image" src="../../assets/images/delete.png" alt="DELETE">
					{{ Form::close() }}	
				</div>	
			</div>
			@if(!$categorie->materials->isEmpty())
			<div class="col-md-12 table-responsive">
				<table class="table">
					<tr>
						<th>Naam</th>
						<th>Status</th>
						<th>Barcode</th>
						<th></th>
						<th></th>
					</tr>
					@foreach($categorie->materials as $material)
					@if ($material->status == "broken")
					<tr class="danger">
					@elseif ($material->status == "missing")
							<tr class="warning">
					@else
						<tr>
					@endif
						<td>{{link_to('materials/'.$material->id,$material->name)}}</td>
						<td>{{$material->status}}</td>
						<td>{{$material->barcode}}</td>
						<td>
							{{ Form::open(['route' => ['materials.edit', $material->id], 'method' => 'GET']) }}
						    <div class="floatleft"><input type="image" src="../../assets/images/edit.png" alt="EDIT"></div>
							{{ Form::close() }}</td>
						<td>
							{{ Form::open(['route' => ['materials.destroy', $material->id], 'method' => 'delete']) }}
						    <div class="floatleft"><input type="image" src="../../assets/images/delete.png" alt="DELETE"></div>
							{{ Form::close() }}	
						</td>	
					</tr>
					@endforeach
				</table>
			</div>
			@else
			<h4 class="notification">Geen materiaal beschikbaar</h4>
			@endif
			
		@empty
		<h4 class="notification">Geen categorieën beschikbaar</h4>
		@endforelse
	</div>

@stop