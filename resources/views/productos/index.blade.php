@extends('layouts.admin')
@section('contenido')
<div class="main-content">
  @include('navbar')

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
                  Productos registrados en el sistema.
                </h6>
                <!-- Title -->
                <h1 class="header-title">
                  Productos
                </h1>
              </div>
              <!-- Si deseas botón para registrar, lo agregas aquí -->
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

        <!-- Busqueda avanzada -->
        <div class="form-group">
          {!! Form::open(['url' => 'panel/data/productos', 'method' => 'GET', 'autocomplete' => 'off', 'role' => 'search']) !!}
            <div class="row">
              <!-- Buscar por nombre -->
              <div class="col-lg-4 mb-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar producto por nombre">
              </div>
              <!-- Filtro por categoría -->
              <div class="col-lg-3 mb-2">
                <select name="categoria" class="form-control">
                  <option value="">Todas las categorías</option>
                  @foreach($categorias as $cat)
                    <option value="{{ $cat->titulo }}" {{ request('categoria') == $cat->titulo ? 'selected' : '' }}>
                      {{ $cat->titulo }}
                    </option>
                  @endforeach
                </select>
              </div>
              <!-- Filtro por estado -->
              <div class="col-lg-2 mb-2">
                <select name="estado" class="form-control">
                  <option value="">Todos los estados</option>
                  <option value="Disponible" {{ request('estado') == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                  <option value="Baja" {{ request('estado') == 'Baja' ? 'selected' : '' }}>Baja</option>
                </select>
              </div>
              <!-- Filtro por activo hosteltáctil -->
              <div class="col-lg-2 mb-2">
                <select name="activo_hosteltactil" class="form-control">
                  <option value="">Hosteltáctil: Todos</option>
                  <option value="1" {{ request('activo_hosteltactil') == '1' ? 'selected' : '' }}>Activo</option>
                  <option value="0" {{ request('activo_hosteltactil') === '0' ? 'selected' : '' }}>No activo</option>
                </select>
              </div>
              <div class="col-lg-1 mb-2">
                <button type="submit" class="btn btn-primary">
                  <span class="fe fe-search"></span>
                </button>
              </div>
            </div>
          {{ Form::close() }}
        </div>

        <!-- Listado de productos -->
        <div class="form-group">
          <div class="row">
            @foreach ($productos as $item)
              <div class="col-12 col-xl-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <!-- Avatar -->
                        <a class="avatar avatar-lg">
                          <img src="{{ asset('admin/'.$item->portada) }}" alt="..." class="avatar-img rounded-circle">
                        </a>
                      </div>
                      <div class="col ml-n2">
                        <!-- Título -->
                        <h4 class="mb-1">
                          <a>{{ $item->titulo }}</a>
                        </h4>
                        <!-- Descripción -->
                        <p class="card-text small text-muted mb-1">
                          {{ $item->descripcion }}
                        </p>
                        <!-- Precio -->
                        <p class="card-text small text-muted mb-1">
                          {{ $item->precio }} €
                        </p>
                        <!-- ID -->
                        <p class="card-text small text-muted mb-1">ID: {{ $item->id }}</p>
                        <!-- Estado -->
                        <p class="card-text small">
                          @if ($item->estado == 'Disponible')
                            <span class="text-success">●</span> {{ $item->estado }}
                          @else
                            <span class="text-danger">●</span> {{ $item->estado }}
                          @endif
                        </p>
                        <!-- Activo Hosteltáctil -->
                        <p class="card-text small">
                          @if ($item->activo_hosteltactil)
                            <span class="text-success">●</span> Activo Hosteltáctil
                          @else
                            <span class="text-danger">●</span> No activo Hosteltáctil
                          @endif
                        </p>
                        <!-- Combinados -->
                        <div>
                          <h4>Combinados:</h4>
                          <ul>
                            @foreach ($item->combinados as $combinado)
                              <li>{{ $combinado->nombrecombi }}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                      <div class="col-auto">
                        @if ($item->estado == 'Disponible')
                          <a data-toggle="modal" data-target="#estado-{{ $item->id }}" class="btn btn-sm btn-danger d-none d-md-inline-block" style="color: white !important">
                            Desactivar
                          </a>
                        @else
                          <a data-toggle="modal" data-target="#estado-{{ $item->id }}" class="btn btn-sm btn-success d-none d-md-inline-block" style="color: white !important">
                            Activar
                          </a>
                        @endif
                      </div>
                      <div class="col-auto">
                        <!-- Dropdown de opciones -->
                        <div class="dropdown">
                          <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true">
                            <i class="fe fe-more-vertical"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ route('edit.producto', $item->id) }}" class="dropdown-item" style="cursor:pointer">
                              <i class="fe fe-edit"></i> Editar producto
                            </a>
                            <a href="{{ route('productos.combinados.edit', $item->id) }}" class="dropdown-item">
                                <i class="fe fe-layers"></i> Editar Jerarquía de Combinados
                            </a>
                            <a href="{{ route('edit.producto_combinados', $item->id) }}" class="dropdown-item" style="cursor:pointer">
                              <i class="fe fe-edit"></i> Editar combinados del producto
                            </a>
                            <a class="dropdown-item" data-toggle="modal" data-target="#modal-{{ $item->id }}" style="cursor:pointer">
                              <i class="fe fe-trash-2"></i> Eliminar
                            </a>
                            @if ($item->oferta == '0')
                              <a class="dropdown-item" data-toggle="modal" data-target="#oferta-{{ $item->id }}" style="cursor:pointer">
                                <i class="fe fe-slack"></i> Ofertar
                              </a>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div> <!-- / .row -->
                  </div> <!-- / .card-body -->
                </div>
              </div>
              @include('productos.modal')
              @include('productos.estado')
              @include('productos.oferta')
            @endforeach

            <div class="col-lg-12 mt-4">
              {{ $productos->appends(request()->all())->links() }}
            </div>
          </div>
        </div>
      </div>
    </div> <!-- / .row -->
  </div>
</div>

@push('scripts')
<script>
  $('.dz-button').text('Subir logo');
  $('.color-picker').spectrum({ type: "text" });
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#blah').attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imgInp").change(function() {
    readURL(this);
  });
</script>
@endpush
@endsection