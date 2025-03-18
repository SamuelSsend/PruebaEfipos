@extends('layouts.admin')
@section('contenido')
<div class="main-content">

  @include('navbar')

  @php
      $activeMenus = $menu->where('activo', 1);
      $inactiveMenus = $menu->where('activo', 0);
  @endphp

  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-8">

        <!-- Header -->
        <div class="header mt-md-5">
          <div class="header-body">
            <div class="row align-items-center">
              <div class="col">
                <!-- Pretitle -->
                <h6 class="header-pretitle">
                  Categorías de menú
                </h6>
                <!-- Title -->
                <h1 class="header-title">
                  Menús disponibles
                </h1>
              </div>
              <div class="col-12 col-md-auto mt-2 mt-md-0 mb-md-3">
                <a href="{{ route('create.menu') }}" class="btn btn-primary d-block d-md-inline-block lift">
                  Registrar
                </a>
              </div>
            </div> <!-- / .row -->
          </div>
        </div>

        <!-- Alertas -->
        <div class="form-group">
          @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ Session::get('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          @endif
        </div>
        <div class="form-group">
          @if(Session::has('danger'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ Session::get('danger') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          @endif
        </div>

        <!-- Categorías activas -->
        <div id="active-menu">
          <h2>Categorías Activas</h2>
          <div class="row" id="sortable-active-menu">
            @foreach ($activeMenus as $item)
              <div class="col-12 col-md-6 col-xl-6 item" data-id="{{ $item->id }}">
                <!-- Card -->
                <div class="card">
                  <!-- Dropdown -->
                  <div class="dropdown card-dropdown">
                    <a href="#!" class="dropdown-ellipses dropdown-toggle text-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fe fe-more-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a href="{{ route('edit.menu', $item->id) }}" class="dropdown-item">
                        <i class="fe fe-edit-3"></i> Editar
                      </a>
                      <a class="dropdown-item" data-toggle="modal" data-target="#modal-{{ $item->id }}" style="cursor:pointer">
                        <i class="fe fe-trash-2"></i> Eliminar
                      </a>
                    </div>
                  </div>

                  <!-- Image -->
                  <img src="{{ asset('admin/'.$item->fondo) }}" alt="..." class="card-img-top">

                  <!-- Body -->
                  <div class="card-body text-center">
                    <!-- Avatar -->
                    <a href="{{ route('inicio') }}/menu/{{ $item->titulo }}" class="avatar avatar-xl card-avatar card-avatar-top">
                      <img src="{{ asset('admin/'.$item->preview) }}" class="avatar-img rounded-circle border border-4 border-card" alt="...">
                    </a>
                    <!-- Heading -->
                    <h2 class="card-title">
                      <a href="{{ route('inicio') }}/menu/{{ $item->titulo }}">{{ $item->titulo }}</a>
                    </h2>
                    <!-- Text -->
                    <p class="small text-muted mb-3">
                      {{ $item->enlace }}
                    </p>
                  </div>
                </div>
              </div>
              @include('configuraciones.menu.modal')
            @endforeach
          </div>
        </div>

        <!-- Línea separadora -->
        <hr style="margin: 40px 0;">

        <!-- Categorías inactivas -->
        <div id="inactive-menu">
          <h2>Categorías Inactivas</h2>
          <div class="row" id="sortable-inactive-menu">
            @foreach ($inactiveMenus as $item)
              <div class="col-12 col-md-6 col-xl-6 item" data-id="{{ $item->id }}">
                <!-- Card con estilo diferenciado -->
                <div class="card" style="opacity: 0.6;">
                  <!-- Dropdown -->
                  <div class="dropdown card-dropdown">
                    <a href="#!" class="dropdown-ellipses dropdown-toggle text-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fe fe-more-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a href="{{ route('edit.menu', $item->id) }}" class="dropdown-item">
                        <i class="fe fe-edit-3"></i> Editar
                      </a>
                      <a class="dropdown-item" data-toggle="modal" data-target="#modal-{{ $item->id }}" style="cursor:pointer">
                        <i class="fe fe-trash-2"></i> Eliminar
                      </a>
                    </div>
                  </div>

                  <!-- Image -->
                  <img src="{{ asset('admin/'.$item->fondo) }}" alt="..." class="card-img-top">

                  <!-- Body -->
                  <div class="card-body text-center">
                    <!-- Avatar -->
                    <a href="{{ route('inicio') }}/menu/{{ $item->titulo }}" class="avatar avatar-xl card-avatar card-avatar-top">
                      <img src="{{ asset('admin/'.$item->preview) }}" class="avatar-img rounded-circle border border-4 border-card" alt="...">
                    </a>
                    <!-- Heading -->
                    <h2 class="card-title">
                      <a href="{{ route('inicio') }}/menu/{{ $item->titulo }}">{{ $item->titulo }}</a>
                    </h2>
                    <!-- Text -->
                    <p class="small text-muted mb-3">
                      {{ $item->enlace }}
                    </p>
                  </div>
                </div>
              </div>
              @include('configuraciones.menu.modal')
            @endforeach
          </div>
        </div>

      </div>
    </div> <!-- / .row -->
  </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
  // Inicializa Sortable para categorías activas
  $("#sortable-active-menu").sortable({
    update: function(event, ui) {
      var order = $(this).sortable('toArray', {attribute: 'data-id'});
      $.ajax({
        url: "{{ route('menu.updateOrder.active') }}",
        method: "POST",
        data: {
          order: order,
          _token: "{{ csrf_token() }}"
        },
        success: function(response) {
          console.log('Orden de categorías activas actualizado');
        },
        error: function() {
          console.log('Error al actualizar el orden de categorías activas');
        }
      });
    }
  });

  // Inicializa Sortable para categorías inactivas
  $("#sortable-inactive-menu").sortable({
    update: function(event, ui) {
      var order = $(this).sortable('toArray', {attribute: 'data-id'});
      $.ajax({
        url: "{{ route('menu.updateOrder.inactive') }}",
        method: "POST",
        data: {
          order: order,
          _token: "{{ csrf_token() }}"
        },
        success: function(response) {
          console.log('Orden de categorías inactivas actualizado');
        },
        error: function() {
          console.log('Error al actualizar el orden de categorías inactivas');
        }
      });
    }
  });
});
</script>
@endpush
@endsection