@extends("global.base")

@section("page-title")
	Overzicht
@stop

@section("nav")
	@include("global.nav")
@stop

@section("content")
	<h2>Beheer Materiaal</h2>
	<div>
		<a href="/materials/create">Materiaal toevoegen</a>
		<a href="/categories/create">Categorie toevoegen</a>
	</div>
@stop