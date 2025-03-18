@extends('layouts.admin')

@section('contenido')
<div class="main-content">
  @include('navbar')
  <div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10 col-xl-8">
        <h1 class="mt-5">Editar combinados: {{ $alimento->titulo }}</h1>

        <!-- Formulario de actualización -->
        <form id="form-editar" action="{{ route('productos.combinados.update', $alimento->id) }}" method="POST">
          @csrf
          @method('PUT')

          <!-- Lista anidada con NestedSortable -->
          <ol id="sortable-combination" class="sortable">
            @foreach($combinaciones as $comb)
              <li class="combination" data-id="{{ $comb->combinado_id }}">
                <div class="item-header">
                  <span class="handle" style="cursor: move; margin-right: 8px;">⇅</span>
                  <strong>{{ $comb->nombrecombi }}</strong>
                  <small>Orden: <span class="orden-text">{{ $comb->orden }}</span> | Multiplicidad: {{ $comb->multiplicidad }}</small>
                  <label style="margin-left: 10px;">
                    Obligatorio
                    <input type="checkbox" name="combinado_obligatorio[{{ $comb->combinado_id }}]" {{ $comb->obligatorio == 1 ? 'checked' : '' }}>
                  </label>
                  <input type="hidden" name="combinado_orden[{{ $comb->combinado_id }}]" class="hidden-orden" value="{{ $comb->orden }}">
                </div>

                <!-- Nivel 2: Subproductos -->
                @if(count($comb->subproductos) > 0)
                  <ol class="sortable-subproducto">
                    @foreach($comb->subproductos as $sub)
                      <li class="subproducto" data-id="{{ $sub->subproducto_id }}">
                        <div class="item-header">
                          <span class="handle" style="cursor: move; margin-right: 8px;">⇅</span>
                          <strong>{{ $sub->nombre }}</strong>
                          <span style="font-style: italic;">(Precio: {{ $sub->precio }})</span>
                          <input type="hidden" name="subproducto_orden[{{ $sub->subproducto_id }}]" class="hidden-orden" value="0">
                        </div>

                        <!-- Nivel 3: Grupos agrupados por subcategoría -->
                        @if(isset($sub->subCombi) && count($sub->subCombi) > 0)
                          <ol class="sortable-subcombi">
                            @foreach($sub->subCombi as $subcategoria => $subcombiGroup)
                              <li class="subcombi-group" data-group="{{ $subcategoria }}" data-subproducto="{{ $sub->subproducto_id }}">
                                <div class="item-header">
                                  <span class="handle" style="cursor: move; margin-right: 8px;">⇅</span>
                                  <strong>{{ $subcategoria }}</strong>
                                  <span>({{ count($subcombiGroup) }} elementos)</span>
                                  <label style="margin-left: 10px;">
                                    Obligatorio
                                    <input type="checkbox" name="subcombi_obligatorio[{{ $sub->subproducto_id }}][{{ $subcategoria }}]"
                                      {{ (isset($subcombiGroup[0]) && $subcombiGroup[0]->seccionobligatoria == 1) ? 'checked' : '' }}>
                                  </label>
                                  <input type="hidden" name="subcombi_orden[{{ $sub->subproducto_id }}][{{ $subcategoria }}]" class="hidden-orden" value="{{ $subcombiGroup[0]->orden ?? 0 }}">
                                </div>
                              </li>
                            @endforeach
                          </ol>
                        @endif

                      </li>
                    @endforeach
                  </ol>
                @endif

              </li>
            @endforeach
          </ol>

          <button type="submit" class="btn btn-success mt-3">Guardar Cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Incluir jQuery, jQuery UI y NestedSortable versión 2.0 desde jsDelivr -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/nestedSortable/2.0.0/jquery.mjs.nestedSortable.min.js"></script>
<script>
$(function(){
  // Inicialización del NestedSortable
  $('#sortable-combination').nestedSortable({
    forcePlaceholderSize: true,
    handle: 'div.item-header',
    items: 'li',
    placeholder: 'placeholder',
    revert: 250,
    toleranceElement: '> div',
    isTree: true,
    expandOnHover: 700,
    startCollapsed: false,
    update: function(event, ui){
      updateOrder();
    }
  });

  // Función que recorre cada nivel y actualiza los inputs hidden con el orden
  function updateOrder() {
    // Nivel 1: Combinaciones
    $('#sortable-combination > li').each(function(index){
      $(this).find('> div.item-header > input.hidden-orden').val(index);
      $(this).find('> div.item-header > .orden-text').text(index);

      // Nivel 2: Subproductos
      $(this).find('ol.sortable-subproducto > li').each(function(i){
        $(this).find('> div.item-header > input.hidden-orden').val(i);

        // Nivel 3: Grupos (subcategorías)
        $(this).find('ol.sortable-subcombi > li.subcombi-group').each(function(j){
          $(this).find('> div.item-header > input.hidden-orden').val(j);
        });
      });
    });
  }
  
  updateOrder(); // Actualiza el orden inicial
});
</script>
@endpush