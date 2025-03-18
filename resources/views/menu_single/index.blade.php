@extends('layouts.user')
@section('user')
<main class="page-content">


    <!-- Breadcrumbs & Page title-->
    <section class="text-center section-34 section-sm-60 section-md-top-100 section-md-bottom-105 bg-image bg-image-breadcrumbs">
      <div class="container">
        <div class="row no-gutters">
          <div class="col-xs-12 col-xl-preffix-1 col-xl-11">
            <p class="h3 text-white">{{$menu_comida->titulo}}</p>
          </div>
        </div>
      </div>
    </section>


    <div class="modal modal-producto" id="modalProducto" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
					<img style="max-width: 100%;" id="modal-imagen" src="//ximg.es/160x160" alt="Imagen de producto">
					<h4 id="modal-titulo"></h4>
                    {{--                    <ul id="modal-ingredientes" class="modal-producto-ingredientes">--}}
                    {{--                        <li>Cheddar cheese</li>--}}
                    {{--                        <li>Lettuce</li>--}}
                    {{--                        <li>Roast beef</li>--}}
                    {{--                    </ul>--}}
                    <div id="modal-alergenos"></div>
                    <div style="display: flex; align-items: center; margin-bottom: 35px;">
                        <span  class="modal-producto-precio"><span id="modal-precio"></span> €</span>
                        <input id="modal-cantidad" type="number" value="1" min="1">
                    </div>
                    <p id="modal-opciones-label">Opciones:</p>
                    <div id="modal-opciones" style="padding-bottom: 20px;">
                    </div>
                    <div id="comment-section">
                        <h5>Comentario:</h5>
                        <textarea maxlength="200" id="comment-input" class="form-control" rows="3" placeholder="Escribe tu comentario aquí..."></textarea>
                    </div>
                    <br>
                    <hr>
                    <div id="notification-container" style="z-index: 2000;"></div>
                    <button id="modal-comprar" type="button" class="boton-comprar">Añadir a la cesta</button>
                    <span class="boton-cerrar" data-dismiss="modal" aria-label="Close">Cerrar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- @foreach($alimentosConCombinados as $item)
        {{$item}}
    @endforeach -->
    <div class="modal modal-producto" id="modalConfirmacion" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p style="margin-bottom: 14px; font-size: 18px; font-weight: bold;" id="modal-mensaje"></p>
                    <hr>
                    <a class="boton-comprar" href="{{route('open_carrito')}}">Ir a la cesta</a>
                    <span class="boton-cerrar" data-dismiss="modal" aria-label="Close">Seguir comprando</span>
                </div>
            </div>
        </div>
    </div>
    @if(Session::has('warning'))
        <div class="alert alert-warning" role="alert">
            <h4>{{ Session::get('warning') }}</h4>
        </div>
    @endif

	<section class="section-top-50 hidden-xs">
		<div class="container" style="overflow: hidden; padding: 0 60px;">
		<div class="bg-navegacion-left"></div>
		<div class="bg-navegacion-right"></div>

			<div class="swiper swiper-categorias">
				<div class="swiper-wrapper">
					@foreach ($categorias as $item)
						<div class="swiper-slide">
							<a class="swiper-categorias-item @if($item->id === $menu_comida->id) active @endif" href="{{route('cliente.menu.show',strtolower($item->titulo))}}">{{$item->titulo}}</a>
						</div>
					@endforeach
				</div>
			</div>

		</div>
	</section>

    <section class="section-50 section-sm-80">
      <div class="container">
        <div class="row justify-content-xs-center">
          <div class="col-xs-12">
				@if (count($alimentosConCombinados) <= '0')
					<div class="menu-item h6"><span><span>No hay ninguno disponible</span></span>:c</div>
					<div class="menu-item-desc"><span>Vuelva mañana!</span></div>
				@else
					<div class="row">
						@foreach ($alimentosConCombinados as $item)
						<div class="col-md-4 item" style="cursor: pointer" data-item="{{ $item }}">
								<div class="thumbnail-menu-modern">
									<figure>
										<img class="img-responsive" style="width: 100%;"
													src="{{asset('admin/'.$item->portada)}}" alt=""
													onerror="this.onerror=null; this.src='{{asset('img/sin-imagen.jpg')}}'"
										/>
									</figure>
									<div class="caption">
										<h5 id="titulo">{{$item->titulo}}</h5>
										<p class="text-italic" id="descripcion">{{$item->descripcion}}</p>
										<p id="price" class="price" id="precio">{{$item->precio}} €</p>
										<div class="section-top-34">
											<!--new-->
											@if(count($item->combinados) == 0)
												<button>
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 1.02857C0 0.755777 0.108367 0.494156 0.301261 0.301261C0.494156 0.108367 0.755777 0 1.02857 0H1.79383C3.09669 0 3.8784 0.876343 4.32411 1.69097C4.62171 2.23406 4.83703 2.86354 5.00571 3.43406C5.05134 3.43046 5.09709 3.42863 5.14286 3.42857H22.283C23.4213 3.42857 24.2441 4.51749 23.9314 5.61326L21.4245 14.4027C21.1997 15.1911 20.7242 15.8848 20.0699 16.3787C19.4156 16.8726 18.6182 17.1399 17.7984 17.1401H9.64114C8.81485 17.1401 8.01141 16.8688 7.35432 16.3678C6.69723 15.8668 6.22286 15.1639 6.00411 14.3671L4.96183 10.5655L3.23383 4.73966L3.23246 4.72869C3.01851 3.95109 2.81829 3.22286 2.51931 2.67977C2.23269 2.15177 2.00229 2.05714 1.7952 2.05714H1.02857C0.755777 2.05714 0.494156 1.94878 0.301261 1.75588C0.108367 1.56299 0 1.30137 0 1.02857ZM6.95726 10.0663L7.9872 13.8226C8.19291 14.5659 8.86903 15.083 9.64114 15.083H17.7984C18.171 15.083 18.5335 14.9615 18.8309 14.7371C19.1284 14.5127 19.3446 14.1974 19.4469 13.8391L21.829 5.48571H5.60229L6.93806 9.9936L6.95726 10.0663ZM11.6571 21.2571C11.6571 21.9846 11.3682 22.6823 10.8538 23.1966C10.3394 23.711 9.64174 24 8.91429 24C8.18684 24 7.48918 23.711 6.97479 23.1966C6.46041 22.6823 6.17143 21.9846 6.17143 21.2571C6.17143 20.5297 6.46041 19.832 6.97479 19.3177C7.48918 18.8033 8.18684 18.5143 8.91429 18.5143C9.64174 18.5143 10.3394 18.8033 10.8538 19.3177C11.3682 19.832 11.6571 20.5297 11.6571 21.2571ZM9.6 21.2571C9.6 21.0753 9.52776 20.9009 9.39916 20.7723C9.27056 20.6437 9.09615 20.5714 8.91429 20.5714C8.73242 20.5714 8.55801 20.6437 8.42941 20.7723C8.30082 20.9009 8.22857 21.0753 8.22857 21.2571C8.22857 21.439 8.30082 21.6134 8.42941 21.742C8.55801 21.8706 8.73242 21.9429 8.91429 21.9429C9.09615 21.9429 9.27056 21.8706 9.39916 21.742C9.52776 21.6134 9.6 21.439 9.6 21.2571ZM21.2571 21.2571C21.2571 21.9846 20.9682 22.6823 20.4538 23.1966C19.9394 23.711 19.2417 24 18.5143 24C17.7868 24 17.0892 23.711 16.5748 23.1966C16.0604 22.6823 15.7714 21.9846 15.7714 21.2571C15.7714 20.5297 16.0604 19.832 16.5748 19.3177C17.0892 18.8033 17.7868 18.5143 18.5143 18.5143C19.2417 18.5143 19.9394 18.8033 20.4538 19.3177C20.9682 19.832 21.2571 20.5297 21.2571 21.2571ZM19.2 21.2571C19.2 21.0753 19.1278 20.9009 18.9992 20.7723C18.8706 20.6437 18.6961 20.5714 18.5143 20.5714C18.3324 20.5714 18.158 20.6437 18.0294 20.7723C17.9008 20.9009 17.8286 21.0753 17.8286 21.2571C17.8286 21.439 17.9008 21.6134 18.0294 21.742C18.158 21.8706 18.3324 21.9429 18.5143 21.9429C18.6961 21.9429 18.8706 21.8706 18.9992 21.742C19.1278 21.6134 19.2 21.439 19.2 21.2571Z" fill="#FFD966"/></svg>
													<span>Añadir a pedido</span>
												</button>
											@else
												<button >
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 1.02857C0 0.755777 0.108367 0.494156 0.301261 0.301261C0.494156 0.108367 0.755777 0 1.02857 0H1.79383C3.09669 0 3.8784 0.876343 4.32411 1.69097C4.62171 2.23406 4.83703 2.86354 5.00571 3.43406C5.05134 3.43046 5.09709 3.42863 5.14286 3.42857H22.283C23.4213 3.42857 24.2441 4.51749 23.9314 5.61326L21.4245 14.4027C21.1997 15.1911 20.7242 15.8848 20.0699 16.3787C19.4156 16.8726 18.6182 17.1399 17.7984 17.1401H9.64114C8.81485 17.1401 8.01141 16.8688 7.35432 16.3678C6.69723 15.8668 6.22286 15.1639 6.00411 14.3671L4.96183 10.5655L3.23383 4.73966L3.23246 4.72869C3.01851 3.95109 2.81829 3.22286 2.51931 2.67977C2.23269 2.15177 2.00229 2.05714 1.7952 2.05714H1.02857C0.755777 2.05714 0.494156 1.94878 0.301261 1.75588C0.108367 1.56299 0 1.30137 0 1.02857ZM6.95726 10.0663L7.9872 13.8226C8.19291 14.5659 8.86903 15.083 9.64114 15.083H17.7984C18.171 15.083 18.5335 14.9615 18.8309 14.7371C19.1284 14.5127 19.3446 14.1974 19.4469 13.8391L21.829 5.48571H5.60229L6.93806 9.9936L6.95726 10.0663ZM11.6571 21.2571C11.6571 21.9846 11.3682 22.6823 10.8538 23.1966C10.3394 23.711 9.64174 24 8.91429 24C8.18684 24 7.48918 23.711 6.97479 23.1966C6.46041 22.6823 6.17143 21.9846 6.17143 21.2571C6.17143 20.5297 6.46041 19.832 6.97479 19.3177C7.48918 18.8033 8.18684 18.5143 8.91429 18.5143C9.64174 18.5143 10.3394 18.8033 10.8538 19.3177C11.3682 19.832 11.6571 20.5297 11.6571 21.2571ZM9.6 21.2571C9.6 21.0753 9.52776 20.9009 9.39916 20.7723C9.27056 20.6437 9.09615 20.5714 8.91429 20.5714C8.73242 20.5714 8.55801 20.6437 8.42941 20.7723C8.30082 20.9009 8.22857 21.0753 8.22857 21.2571C8.22857 21.439 8.30082 21.6134 8.42941 21.742C8.55801 21.8706 8.73242 21.9429 8.91429 21.9429C9.09615 21.9429 9.27056 21.8706 9.39916 21.742C9.52776 21.6134 9.6 21.439 9.6 21.2571ZM21.2571 21.2571C21.2571 21.9846 20.9682 22.6823 20.4538 23.1966C19.9394 23.711 19.2417 24 18.5143 24C17.7868 24 17.0892 23.711 16.5748 23.1966C16.0604 22.6823 15.7714 21.9846 15.7714 21.2571C15.7714 20.5297 16.0604 19.832 16.5748 19.3177C17.0892 18.8033 17.7868 18.5143 18.5143 18.5143C19.2417 18.5143 19.9394 18.8033 20.4538 19.3177C20.9682 19.832 21.2571 20.5297 21.2571 21.2571ZM19.2 21.2571C19.2 21.0753 19.1278 20.9009 18.9992 20.7723C18.8706 20.6437 18.6961 20.5714 18.5143 20.5714C18.3324 20.5714 18.158 20.6437 18.0294 20.7723C17.9008 20.9009 17.8286 21.0753 17.8286 21.2571C17.8286 21.439 17.9008 21.6134 18.0294 21.742C18.158 21.8706 18.3324 21.9429 18.5143 21.9429C18.6961 21.9429 18.8706 21.8706 18.9992 21.742C19.1278 21.6134 19.2 21.439 19.2 21.2571Z" fill="#FFD966"/></svg>
													<span>Añadir a pedido</span>
												</button>
											@endif
										</div>

									</div>
								</div>
							</div>
                    	@endforeach
			</div>
				@endif
				<div class="section-top-105">
					<a class="boton-volver-a-menu" href="{{route('cliente.menu.index')}}">
						<svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M7.41 10.59L2.83 6L7.41 1.41L6 7.15493e-08L7.15493e-08 6L6 12L7.41 10.59Z" fill="#CC0000"/>
						</svg>
						<span>Volver al menú</span>
					</a>
				</div>
          </div>
        </div>

      </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asigna evento click a cada elemento con la clase .item
    document.querySelectorAll(".item").forEach(link => {
        $(link).click(function(event) {
            // Parsear la información JSON almacenada en el atributo data-item
            const item = JSON.parse(this.dataset.item);
            $("#modal-comprar").attr("data-id", item.id);
            
            // Configurar imagen y título del modal
            $("#modal-imagen").attr("src", item.portada ? "https://thebeastburgeralg.efiposdelivery.com.es/admin/" + item.portada : '/img/sin-imagen.jpg');
            $("#modal-titulo").text(item.titulo);
            $("#modal-opciones").html("");
            $("#modal-alergenos").html("");

            // Mostrar alérgenos si existen
            if(Object.keys(item.alergenoImg).length > 0) {
                $("#modal-alergenos").html(`<span class="categoria" style="font-weight: bold;"> Alergenos: </span><br>`);
            }
            item.alergenoImg.forEach(img => {
                $("#modal-alergenos").append(`<img src="data:image/png;base64, ${img.imagen}" alt="alergeno">`);
            });

            // Recorrer cada combinación (categoría padre)
            let subproductosVistos = {};
            let combinaciones = Array.isArray(item.combinaciones)
                ? item.combinaciones
                : Object.values(item.combinaciones);
            
            combinaciones.forEach(combinado => {
                let obligatorioValue = (combinado.obligatorio == 1 || combinado.obligatorio === true) ? 1 : 0;
                let obligatorioText = obligatorioValue ? ' - Obligatorio' : '';
                let cat = `categoria-${combinado.combinado_id}`;
                $("#modal-opciones").append(
                    `<div class="categoria" id="${cat}" data-obligatorio="${obligatorioValue}" style="font-weight: bold;">
                         ${combinado.nombrecombi} (Máx: ${combinado.multiplicidad})${obligatorioText}
                     </div>`
                );

                // Recorrer cada subproducto asociado a la combinación
                combinado.subproductos.forEach(subproducto => {
                    if(subproductosVistos[subproducto.id]) return;
                    subproductosVistos[subproducto.id] = true;
                    
                    let subCombiHtml = '';
                    let subCombiSections = {};

                    // Si existe la propiedad subCombi, convertirla a array (por si viene como objeto) y agrupar por subcategoría
                    if(subproducto.subCombi && typeof subproducto.subCombi === 'object') {
                        let subCombiArray = Array.isArray(subproducto.subCombi) ? subproducto.subCombi : Object.values(subproducto.subCombi);
                        subCombiArray.forEach(subCombi => {
                            let sectionName = subCombi.subcategoria; // O usa subCombi.nombre_subcategoria si lo prefieres
                            if(!subCombiSections[sectionName]) {
                                // Guarda el máximo y la obligatoriedad de esta sección
                                subCombiSections[sectionName] = {
                                    max: subCombi.multiplicidad,
                                    obligatorio: subCombi.seccionobligatoria, // valor proveniente de la BDD
                                    items: []
                                };
                            }
                            subCombiSections[sectionName].items.push(subCombi);
                        });

                        // Construir el HTML para cada grupo de subcombinaciones (inicialmente oculto)
                        Object.keys(subCombiSections).forEach(section => {
                            let subMax = subCombiSections[section].max;
                            let obligatorioSubText = subCombiSections[section].obligatorio ? ' - Obligatorio' : '';
                            subCombiHtml += `
                                        <div class="subcategoria subcategoria-${subproducto.id}" 
                                             data-subobligatorio="${subCombiSections[section].obligatorio}"
                                             data-subcatname="${section}"
                                             style="display: none; font-weight: bold; margin-left: 20px;">
                                          ${section} (Máx: ${subMax}${obligatorioSubText})
                                        </div>`;

                            subCombiSections[section].items.forEach(subCombi => {
                                subCombiHtml += `
                                        <div class="modal-subcombi-checkbox custom-control custom-switch subcombi-${subproducto.id}" 
                                             style="display: none; margin-left: 20px;" 
                                             id="subcombi_${subproducto.id}_${subCombi.subproducto_id}">
                                          <input class="custom-control-input subcombi-input" 
                                                 type="checkbox" 
                                                 id="subingrediente_${subproducto.id}_${subCombi.subproducto_id}" 
                                                 value="${subCombi.subproducto_id}"
                                                 data-cate="${cat}" 
                                                 data-subcat="${section}" 
                                                 data-subproducto="${subproducto.id}"
                                                 data-precio="${subCombi.precio}"
                                                 data-multi="${subCombi.multiplicidad}">
                                          <label class="custom-control-label" for="subingrediente_${subproducto.id}_${subCombi.subproducto_id}">
                                              ${subCombi.titulo} (+${subCombi.precio}€)
                                          </label>
                                        </div>`;

                            });
                        });
                    }
                    
                    // Mostrar el checkbox principal del subproducto junto con su bloque de subcombinaciones
                    $("#modal-opciones").append(`
                        <div class="modal-producto-checkbox custom-control custom-switch">
                            <input class="custom-control-input" type="checkbox" id="ingrediente_${subproducto.id}" value="${subproducto.id}" data-multiplicidad="${combinado.multiplicidad}" data-cat="${cat}">
                            <label class="custom-control-label" for="ingrediente_${subproducto.id}">${subproducto.nombre} (+${subproducto.precio}€)</label>
                            <div id="sub_${subproducto.id}">
                                ${subCombiHtml}
                            </div>
                        </div>
                    `);

                    // Al seleccionar/deseleccionar el subproducto, se muestran u ocultan sus subcombinaciones
                    $(`#ingrediente_${subproducto.id}`).change(function() {
                        if($(this).is(':checked')) {
                            $(`.subcombi-${subproducto.id}`).show();
                            $(`.subcategoria-${subproducto.id}`).show();
                        } else {
                            $(`.subcombi-${subproducto.id}`).hide();
                            $(`.subcategoria-${subproducto.id}`).hide();
                            $(`.subcombi-${subproducto.id} input[type='checkbox']`).prop('checked', false);
                        }
                    });
                });
            });

            // Controlar el límite de selección en la categoría padre (excluyendo los inputs de subcombinación)
            $("#modal-opciones").on('change', '.custom-control-input', function() {
                if($(this).hasClass("subcombi-input")) return; // Excluir subcombinaciones
                let currentMulti = $(this).data('multiplicidad');
                let currentCat = $(this).data('cat');
                let selectedCount = contadorCheckBox(currentCat);
                if(selectedCount > currentMulti) {
                    $(`#${currentCat} input[type="checkbox"]:not(:checked)`).prop('disabled', true);
                    if (!$(`#${currentCat} .text-danger`).length) {
						let alerta = document.createElement("div");
						alerta.className = "text-danger";
						alerta.innerHTML = `Solo puede seleccionar ${currentMulti} opción(es)`;
						$(`#${currentCat}`).append(alerta);
						$("#modal-comprar").prop("disabled", true);
						document.getElementById("modal-comprar").style.backgroundColor = "#dddddd";
					}
                } else {
                    $(`#${currentCat} input[type="checkbox"]`).prop('disabled', false);
                    $(`#${currentCat} .text-danger`).remove();
					if ($('.text-danger').length === 0) {
						$("#modal-comprar").prop("disabled", false);
						document.getElementById("modal-comprar").style.backgroundColor = "#cc0000";
					}
                }
            });

            // Controlar el límite de selección en cada grupo de subcombinaciones (por subcategoría)
            $("#modal-opciones").on('change', '.subcombi-input', function() {
                let currentMulti = $(this).data('multi');
                let currentSubCat = $(this).data('subcat');
                let currentCat = $(this).data('cate');
                let currentSub = $(this).data('subproducto');
                let selectedCountSubCombi = contadorCheckBoxSubCombi(currentSubCat, currentCat, currentSub);
                if(selectedCountSubCombi > currentMulti) {
                    $(`.subcombi-${currentSub} input[type="checkbox"]:not(:checked)`).prop('disabled', true);
                    if (!$(`.text-danger-subcombi-${currentSub}`).length) {
						let alerta = document.createElement("div");
						alerta.className = `text-danger text-danger-subcombi-${currentSub}`;
						alerta.innerHTML = `Solo puede seleccionar ${currentMulti} ${currentSubCat}`;
						$(`#sub_${currentSub}`).append(alerta); 
						$("#modal-comprar").prop("disabled", true);
						document.getElementById("modal-comprar").style.backgroundColor = "#dddddd";
					}
                } else {
                    $(`.subcombi-${currentSub} input[type="checkbox"]`).prop('disabled', false);
                    $(`.text-danger-subcombi-${currentSub}`).remove();
					if ($('.text-danger').length === 0) {
						$("#modal-comprar").prop("disabled", false);
						document.getElementById("modal-comprar").style.backgroundColor = "#cc0000";
					}
                }
            });

            // Mostrar u ocultar la etiqueta de opciones según existan combinaciones
            if(item.combinaciones.length > 0) {
                $("#modal-opciones-label").show();
            } else {
                $("#modal-opciones-label").hide();
            }

            // Establecer el precio del producto y mostrar el modal
            $("#modal-precio").html(item.precio);
            $("#modalProducto").modal();
        });
    });


    $("#modal-comprar").click(function() {
        const id = $(this).data("id");
        const cantidad = $("#modal-cantidad").val();
        const comentario = $("#comment-input").val();
        
        let errores = []; 
        
        // Recorremos cada categoría obligatoria (producto padre)
        $(".categoria[data-obligatorio='1']").each(function() {
            let catId   = $(this).attr("id");
            let catName = $(this).text().trim();
            
            // Verificamos si el checkbox del producto padre está marcado          
            let parentCount = $(`input[data-cat="${catId}"]:checked`).length;
            if (parentCount === 0) {
                errores.push(`Debes seleccionar al menos un producto en la categoría: ${catName}`);
            }
            
            $(`#${catId} ~ .modal-producto-checkbox`).find(".subcategoria[data-subobligatorio='1']").each(function() {
                let subcatName = $(this).data("subcatname");

                let subcatCount = $(`.subcombi-input[data-cate="${catId}"][data-subcat="${subcatName}"]:checked`).length;
                if (subcatCount === 0) {
                    errores.push(`Debes seleccionar al menos una opción en la subcategoría "${subcatName}" de ${catName}`);
                }
            });
        });
        
        if (errores.length > 0) {
            mostrarMensaje(errores.join("<br>"));
            return false;
        }
        
        // Si todo está correcto, se recogen los valores y se realiza el post
        let subproductos = $("#modal-opciones input:checked").map(function() {
            return this.value;
        }).get();
        
        $.post(`/ordenar-online/${id}/${cantidad}`, {
            _token: '{{ csrf_token() }}',
            subproductos: subproductos,
            comentario: comentario
        }).then(function(data) {
            $("#modal-mensaje").text(data.message);
            $("#modalConfirmacion").modal();
            $("#modalProducto").modal('hide');
            $("#num-carrito").text(data.num_carrito);
        }).fail(function() {
            mostrarMensaje("Ocurrió un error al procesar tu pedido.");
        });
    });



    // Reiniciar la cantidad cuando se oculta el modal
    $("#modalProducto").on('hidden.bs.modal', function () {
        $("#modal-cantidad").val(1);
    });
});

function mostrarMensaje(mensaje) {
    var notification = $('<div class="alert alert-danger" role="alert" style="min-width: 300px;"></div>');
    notification.append(mensaje);
    $('#notification-container').append(notification);
    notification.delay(3000).fadeOut(500, function() { $(this).remove(); });
}

// Función que cuenta los checkbox seleccionados en una categoría padre
function contadorCheckBox(cat) {
    return $(`.custom-control-input[data-cat="${cat}"]:checked`).length;
}

// Función que cuenta los checkbox seleccionados en un grupo de subcombinaciones (filtrados por data-cate, data-subcat y data-subproducto)
function contadorCheckBoxSubCombi(subcat, cat, subproductoId) {
    return $(`.subcombi-input[data-cate="${cat}"][data-subcat="${subcat}"][data-subproducto="${subproductoId}"]:checked`).length;
}

// Inicializar Swiper para la navegación por categorías (si se utiliza)
var swiper = new Swiper(".swiper-categorias", {
    mousewheel: true,
    slidesPerView: "auto",
    freeMode: true,
    spaceBetween: 20
});
</script>
@endpush
