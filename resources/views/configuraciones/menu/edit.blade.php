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
                    Formulario
                  </h6>

                  <!-- Title -->
                  <h1 class="header-title">
                    Editar menú - {{$menu->titulo}}
                  </h1>

                </div>

              </div> <!-- / .row -->
            </div>
          </div>

          <!-- Form -->
          <form class="mb-4" action="{{route('update.menu',$menu->id)}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group">
              @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{Session::get('success')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
              @endif
            </div>
            <div class="form-group">
              @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{Session::get('danger')}}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
                </div>
              @endif
            </div>

            <div class="form-group">
              <div class="custom-control custom-switch">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" @if($menu->activo) checked @endif>
                <label class="custom-control-label" for="activo">Activo</label>
              </div>
            </div>

            <!-- Team name -->
            <div class="form-group">
              <div class="card">
                <div class="card-body">
                  <input type="text" class="form-control form-control-flush" name="titulo" placeholder="Título del tipo de menú" value="{{$menu->titulo}}" readonly>
                  @if ($errors->has('titulo'))
                    <div class="invalid-feedback">
                      {{ $errors->first('titulo') }}
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="card">
                    <div class="card-body">
                      <input type="text" class="form-control form-control-flush" name="enlace" placeholder="Enlace del menú" value="{{$menu->enlace}}">
                      @if ($errors->has('enlace'))
                        <div class="invalid-feedback">
                          {{ $errors->first('enlace') }}
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <label><b>Vista Previa</b></label>
                      <input type="file" name="preview" class="files">
                      <button type="button" class="btn btn-secondary mt-2" data-toggle="modal" data-target="#selectPreviewModal">
                        Seleccionar desde la base de datos
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-body">
                      <label><b>Fondo</b></label>
                      <input type="file" name="fondo" class="files">
                      <button type="button" class="btn btn-secondary mt-2" data-toggle="modal" data-target="#selectFondoModal">
                        Seleccionar desde la base de datos
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Divider -->
            <hr class="mt-4 mb-5">

            <button type="submit" class="btn btn-block btn-primary">Actualizar</button>
            <a href="#" class="btn btn-block btn-link text-muted">
              Cancelar
            </a>

          </form>

        </div>
      </div> <!-- / .row -->
    </div>

  </div>

  <!-- Modal para seleccionar vista previa desde la base de datos -->
  <div class="modal fade" id="selectPreviewModal" tabindex="-1" role="dialog" aria-labelledby="selectPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="selectPreviewModalLabel">Seleccionar Vista Previa</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('select.photo') }}" method="POST">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <div class="form-group">
              <label for="preview">Seleccionar Vista Previa</label>
              <select class="form-control" id="preview" name="photo">
                @foreach($photos as $photo)
                  <option value="{{ $photo->id }}">{{ $photo->titulo }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleccionar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para seleccionar fondo desde la base de datos -->
  <div class="modal fade" id="selectFondoModal" tabindex="-1" role="dialog" aria-labelledby="selectFondoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="selectFondoModalLabel">Seleccionar Fondo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('select.photo') }}" method="POST">
            @csrf
            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
            <div class="form-group">
              <label for="fondo">Seleccionar Fondo</label>
              <select class="form-control" id="fondo" name="photo">
                @foreach($photos as $photo)
                  <option value="{{ $photo->id }}">{{ $photo->titulo }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleccionar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    $('.dz-button').text('Subir portada');

    $(document).ready(function() {
      $('input.files').fileuploader({
        // Options will go here
      });
    });
  </script>
  @endpush
@endsection
