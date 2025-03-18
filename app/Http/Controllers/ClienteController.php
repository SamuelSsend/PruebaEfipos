<?php

namespace App\Http\Controllers;

use App\Alimento;
use App\Carrito;
use App\CarritoSubproducto;
use App\Combinado;
use App\ConfigGeneral;
use App\HostelTactil\Estado;
use App\HostelTactil\Carta;
use App\Jobs\EnviarPedidoJob;
use App\MenuComida;
use App\Pedido;
use App\PedidoDetalle;
use App\Subproducto;
use Carbon\Carbon;
use Culqi;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class ClienteController extends Controller
{

    public function __construct()
    {
        if (!session()->has('iduser')) {
            session()->put('iduser', Uuid::uuid4());
        }

        //        $this->middleware('auth')->only('ordenar_online', 'add_cart', 'open_carrito', 'destroy_carrito', 'generar_pedido', 'ordenes', 'hoy');
    }
    
    public function index(Request $request)
    {

        $config = DB::table('config_general')
            ->first();

        $slider = DB::table('slider')
            ->get();

        $seccion_uno = DB::table('seccion_uno')
            ->get();

        $menu_comida = DB::table('menu_comida')->whereActivo(1)
            ->get();

        $alimento = Alimento::with('combinados')->get();

        $num_menu = count($menu_comida);

        $inicio = DB::table('inicio')
            ->first();

        $seccion_tres = DB::table('seccion_tres')
            ->get();

        $galeria = DB::table('galeria')
            ->take('4')
            ->get();

            $hora_actual = Carbon::now();
            $dia_actual = ucfirst(Carbon::now()->locale('es')->dayName);
            
            $horarios = DB::table('horario')
                ->where('dia', '=', $dia_actual)
                ->orderBy('desde')
                ->get();
    
            $pedidoPermitido = false;
            $proxDesde = Carbon::now();
            $proxHasta = Carbon::now();
            $proxDia = "";
            
        foreach ($horarios as $horario) {
            if ($horario->cerrado == 0) {
                $desde = Carbon::createFromFormat('H:i:s', $horario->desde);
                $hasta = Carbon::createFromFormat('H:i:s', $horario->hasta);
                    
                if ($hora_actual->between($desde, $hasta)) {
                    $pedidoPermitido = true;
                    break;
                } elseif ($hora_actual->lessThan($desde)) {
                    $proxDesde = $desde;
                    $proxHasta = $hasta;
                    $proxDia = $dia_actual;
                    break;
                }
            }
        }
    
        if (!$pedidoPermitido && $proxDia == "") {
            for ($i = 1; $i <= 7; $i++) {
                $proxDiaCarbon = $hora_actual->copy()->addDays($i);
                $proxDiaNombre = ucfirst($proxDiaCarbon->locale('es')->dayName);
    
                $horariosProxDia = DB::table('horario')
                    ->where('dia', '=', $proxDiaNombre)
                    ->orderBy('desde')
                    ->get();
    
                foreach ($horariosProxDia as $horario) {
                    if ($horario->cerrado == 0) {
                        $proxDesde = Carbon::createFromFormat('H:i:s', $horario->desde)->setDateFrom($proxDiaCarbon);
                        $proxHasta = Carbon::createFromFormat('H:i:s', $horario->hasta)->setDateFrom($proxDiaCarbon);
                        $proxDia = $proxDiaNombre;
                        break 2;
                    }
                }
            }
        }
    
        if (!$pedidoPermitido) {
            Session::flash('warning', 'La próxima apertura será el ' . $proxDia . ' de ' . $proxDesde->format('H:i') . ' hasta ' . $proxHasta->format('H:i'));
        }
        
        /* ---------------------------------------------- */


        /* ---------------------------------------------- */
        return view('inicio', compact('config', 'slider', 'seccion_uno', 'menu_comida', 'num_menu', 'alimento', 'inicio', 'seccion_tres', 'galeria'));
    }

    function faq()
    {
        $faq = DB::table('faq')->get();
        return view('faq', compact('faq'));
    }


    function ordenar_online()
    {

        $menu_comida = DB::table('menu_comida')
            ->whereActivo(1)
            ->get();

        $alimento = Alimento::query()
            ->get();

        return view('orden_online', compact('menu_comida', 'alimento'));
    }

    public function add_cart(Request $request, $idalimento, $cantidad = 1)
    {
        $subproductos = $request->input('subproductos', []);
        $comentario = $request->input('comentario', null);

        $alimento = Alimento::query()
            ->where('id', '=', $idalimento)
            ->first();

        // Fetch the cart item with exact combination of subproductos
        $carrito = Carrito::where('idalimento', $idalimento)
            ->where('uuid', session()->get('iduser'))
            ->where('estado', 'En el carrito')
            ->whereHas(
                'subproductos', function ($query) use ($subproductos) {
                    foreach ($subproductos as $subproductoId) {
                        $query->where('carrito_subproductos.subproducto_id', '=', $subproductoId);
                    }
                }, '=', count($subproductos)
            ) // Ensure the number of subproductos matches exactly
            ->first();

        if ($carrito) {
            // If the item already exists, update the quantity and comentario.
            $carrito->cantidad += $cantidad;
            $carrito->comentario = $comentario;
        } else {
            // If the item does not exist, create a new entry.
            $carrito = new Carrito;
            $carrito->producto = $alimento->titulo;
            $carrito->uuid = session()->get('iduser');
            $carrito->iduser = optional(auth()->user())->id;
            $carrito->idalimento = $idalimento;
            $carrito->estado = 'En el carrito';
            $carrito->precio = substr($alimento->precio, 0, 7);
            $mytime = Carbon::now();
            $carrito->createAt = $mytime->format('Y-m-d H:i:s');
            $carrito->cantidad = $cantidad;
            $carrito->comentario = $comentario;
            $carrito->save();

            $precio = $carrito->precio;

            // Save subproductos
            foreach ($subproductos as $subproductoId) {
                $subproducto = Subproducto::query()
                    ->where('id', '=', $subproductoId)
                    ->firstOrFail();

                $carritoSubproducto = new CarritoSubproducto();
                $carritoSubproducto->registro_id = $carrito->id;
                $carritoSubproducto->subproducto_id = $subproductoId;
                $carritoSubproducto->save();

                $precio += $subproducto->precio * $cantidad;
            }

            $carrito->precio = $precio;
            $carrito->save();
        }

        return response()->json(
            [
            'status' => 'success',
            'message' => '¡Se ha agregado a la cesta exitosamente!',
            'num_carrito' => Carrito::query()
                ->where('uuid', session()->get('iduser'))
                ->where('estado', 'En el carrito')
                ->sum('cantidad')
            ]
        );
    }

    //new

    function show_producto($id)
    {
        $producto = Alimento::findOrFail($id);

        $combinados = $producto->combinados;

        return view('producto', compact('producto', 'combinados'));
    }

    function add_cart_subproductos($idalimento, Request $request)
    {
        $alimento = Alimento::query()
            ->where('id', '=', $idalimento)
            ->first();


        $carrito = new Carrito;
        $carrito->producto = $alimento->titulo;
        $carrito->uuid = session()->get('iduser');
        $carrito->iduser = optional(auth()->user())->id;
        $carrito->idalimento = $idalimento;
        $carrito->estado = 'En el carrito';
        $carrito->precio = substr($alimento->precio, 0, 7);
        $mytime = Carbon::now();
        $carrito->createAt = $mytime->format('Y-m-d H:i:s');
        $carrito->cantidad = '1';
        $carrito->save();

        $subproductos = $request->input('subproductos', []);
        foreach ($subproductos as $subproductoId) {
            $carritoSubproducto = new CarritoSubproducto();
            $carritoSubproducto->registro_id = $carrito->id;
            $carritoSubproducto->subproducto_id = $subproductoId;
            $carritoSubproducto->save();
            
        }

        Session::flash('success', 'Se agregó al carrito exitosamente');
        return redirect(route('open_carrito'));

    }

    function edit_producto($id)
    {
        $carrito = Carrito::findOrFail($id);
        $producto = Alimento::findOrFail($carrito->idalimento);

        $combinados = $producto->combinados;

        return view('producto_sub_edit', compact('carrito', 'combinados'));
    }

    function update_producto(Request $request, $id)
    {
        CarritoSubproducto::where('registro_id', $id)->delete();
        $subproductosSeleccionados = $request->input('subproductos', []);
        foreach ($subproductosSeleccionados as $subproductoId) {
            $carritoSubproducto = new CarritoSubproducto();
            $carritoSubproducto->registro_id = $id;
            $carritoSubproducto->subproducto_id = $subproductoId;
            $carritoSubproducto->save();
        }
        return redirect(route('open_carrito'));
    }


    function open_carrito()
    {
        $config = DB::table('config_general')->first();

        $estadoTPV = Estado::comprobarEstadoTPV();

        $carrito = Carrito::with('subproductos')
            ->join('alimento as a', 'a.id', '=', 'carrito.idalimento')
            ->select('carrito.id', 'a.portada', 'a.titulo as alimento', 'carrito.cantidad', 'carrito.estado', 'carrito.createAt', 'carrito.precio', 'carrito.idalimento', 'carrito.comentario')
            ->where('uuid', '=', session()->get('iduser'))
            ->orderBy('carrito.id', 'desc')
            ->get();

        $total = DB::table('carrito as c')
            ->select(DB::raw("sum(c.precio * c.cantidad) as total"))
            ->where('uuid', '=', session()->get('iduser'))
            ->first();
            
        $totalPrecio = $total->total ?? 0;
        
        $gastosDeEnvio = Alimento::find($config->gastos_de_envio_id);
        $precioEnvio = $gastosDeEnvio ? $gastosDeEnvio->precio : 0;

        $totalAbsoluto = $totalPrecio + $precioEnvio;

        $productos = [];

        foreach ($carrito as $item) {
            array_push($productos, $item->idalimento);
        }

        $data_productos = implode('-', $productos);

        return view('carrito', compact('carrito', 'total', 'totalAbsoluto', 'config', 'data_productos', 'estadoTPV'));
    }



    public function destroy_carrito($id)
    {
        $carrito = Carrito::findOrFail($id);
        $carrito->destroy($id);

        return redirect()->back();
    }

    public function generar_pedido($productos, $iduser, $direccion, $total, $token, $metodo)
    {
        $config = DB::table('config_general')
            ->first();
        //trim(trim(round($total),0), '.')

        if ($metodo == 'culqi') {
            try {
                $SECRET_KEY = $config->{'culqi_private'};
                $culqi = new Culqi\Culqi(array('api_key' => $SECRET_KEY));

                $charge = $culqi->Charges->create(
                    array(
                        "amount" => round($total) . '00',
                        "capture" => true,
                        "currency_code" => 'USD',
                        "description" => $config->{'nombre_empresa'},
                        "email" => "test@culqi.com",
                        "source_id" => $token
                    )
                );
            } catch (\Exception $e) {
                dd($e);
                Session::flash('danger', 'Se rechazó la tarjera de crédito, intente con otra.');
                return Redirect::back();
            }
        }
        $productos = explode("-", $productos);


        $today = getdate();
        $data_month = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $current_month = $today['mon'];
        $current_year = $today['year'];

        $pedido = new Pedido;
        $pedido->iduser = $iduser === 'guest' ? null : $iduser;
        $pedido->uuid = session()->get('iduser');
        $mytime = Carbon::now();
        $pedido->fecha = $today['year'] . '-' . $today['mday'] . '-' . $today['mon'];
        $pedido->direccion = trim($direccion);
        $pedido->total_pagado = $total;
        $pedido->estado = 'En espera';
        $pedido->mes = $current_month;
        $pedido->year = $current_year;
        $pedido->tiempo_estimado = 'Calculando';
        $pedido->save();

        $cont = 0;

        $carrito = DB::table('carrito')
            ->where('uuid', '=', session()->get('iduser'))
            ->get();


        while ($cont < count($productos)) {

            $alimento = Alimento::findOrFail($productos[$cont]);

            $detalle = new PedidoDetalle;
            $detalle->idpedido = $pedido->id;
            $detalle->producto = $alimento->titulo;
            $detalle->precio = substr($alimento->precio, 1, 8);;
            $detalle->save();

            $carrito_del = Carrito::findOrFail($carrito[$cont]->id);
            $carrito_del->delete();

            $cont = $cont + 1;
        }

        return redirect()->route('hoy');


    }

    public function ordenes()
    {

        $pedidos = DB::table('pedido_detalle as d')
            ->join('pedidos as p', 'd.idpedido', '=', 'p.id')
            ->select('p.fecha', 'p.total_pagado', 'p.estado', 'd.producto', 'd.precio', 'p.direccion')
            ->where('uuid', '=', session()->get('iduser'))
            ->orderby('d.id', 'desc')
            ->paginate(20);

        return view('ordenes', compact('pedidos'));
    }

    public function hoy()
    {
        $formattedDate = now()->format('Y-m-d');
        $userId = session()->get('iduser');
    
        try {
            // Ejecutamos el Dispatch del Job de forma inmediata
            dispatch_now(new EnviarPedidoJob());
    
            // Verificamos si el Job ha marcado algún error a través de la sesión
            if (session()->has('error')) {
                $errorMessage = session()->get('error');
                session()->forget('error'); // Limpiamos el mensaje, para evitar duplicados
                if (session()->has('source')) {
                    session()->forget('source');
                    return response()->json([
                        'success' => false,
                        'error'   => $errorMessage,
                    ]);
                }
                return redirect()->back()->with('error', $errorMessage);
            }
    
            // Si llegó hasta aquí, se supone que el Job se ha completado con éxito.
            // Obtenemos el id del último pedido del usuario.
            $id = Pedido::query()->where('uuid', $userId)->latest('id')->value('id');
            
            // Asignamos un flash message de éxito
            session()->flash('success', 'Su pedido se ha enviado correctamente.');
            
            if (session()->has('source')) {
                session()->forget('source');
                return response()->json([
                    'success' => true,
                    'id' => $id
                ]);
            }
            
            return redirect()->route('mostrar_hoy', ['id' => $id])->with([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en EnviarPedidoJob: ' . $e->getMessage());
            
            session()->flash('error', 'Hubo un problema, contacte con nosotros para confirmar si el pago se ha realizado correctamente');
            
            if (session()->has('source')) {
                session()->forget('source');
                return response()->json([
                    'success' => false,
                    'error'   => 'Hubo un problema, contacte con nosotros para confirmar si el pago se ha realizado correctamente'
                ]);
            }
            return redirect()->back()->with('error', 'Hubo un problema, contacte con nosotros para confirmar si el pago se ha realizado correctamente');
        }
    }



    public function ofertas()
    {

        $oferta = Alimento::query()
            ->where('oferta', '=', '1')
            ->orderby('id', 'desc')
            ->get();

        $seccion_uno = DB::table('seccion_uno')
            ->get();

        return view('ofertas', compact('oferta', 'seccion_uno'));
    }

    public function menuIndex()
    {
        $menu_comida = MenuComida::whereActivo(1)->get();
        $general = ConfigGeneral::findOrFail(1);

        return view('menu.index', compact('menu_comida', 'general'));
    }

    function verificarPedidoPermitido()
    {
        $hora_actual = Carbon::now();
        $dia_actual = ucfirst(Carbon::now()->locale('es')->dayName);
    
        $horarios = DB::table('horario')
            ->where('dia', '=', $dia_actual)
            ->orderBy('desde')
            ->get();
    
        $pedidoPermitido = false;
        $proxDesde = Carbon::now();
        $proxHasta = Carbon::now();
        $proxDia = "";
    
        foreach ($horarios as $horario) {
            if ($horario->cerrado == 0) {
                $desde = Carbon::createFromFormat('H:i:s', $horario->desde);
                $hasta = Carbon::createFromFormat('H:i:s', $horario->hasta);
    
                if ($hora_actual->between($desde, $hasta)) {
                    $pedidoPermitido = true;
                    break;
                } elseif ($hora_actual->lessThan($desde)) {
                    $proxDesde = $desde;
                    $proxHasta = $hasta;
                    $proxDia = $dia_actual;
                    break;
                }
            }
        }
    
        if (!$pedidoPermitido && $proxDia == "") {
            for ($i = 1; $i <= 7; $i++) {
                $proxDiaCarbon = $hora_actual->copy()->addDays($i);
                $proxDiaNombre = ucfirst($proxDiaCarbon->locale('es')->dayName);
    
                $horariosProxDia = DB::table('horario')
                    ->where('dia', '=', $proxDiaNombre)
                    ->orderBy('desde')
                    ->get();
    
                foreach ($horariosProxDia as $horario) {
                    if ($horario->cerrado == 0) {
                        $proxDesde = Carbon::createFromFormat('H:i:s', $horario->desde)->setDateFrom($proxDiaCarbon);
                        $proxHasta = Carbon::createFromFormat('H:i:s', $horario->hasta)->setDateFrom($proxDiaCarbon);
                        $proxDia = $proxDiaNombre;
                        break 2;
                    }
                }
            }
        }
    
        if (!$pedidoPermitido) {
            Session::flash('warning', 'La próxima apertura será el ' . $proxDia . ' de ' . $proxDesde->format('H:i') . ' hasta ' . $proxHasta->format('H:i'));
        }
    
    }
        
    function menu_single($menu)
    {
        // 1. Obtener categorías activas
        $categorias = Cache::remember('categorias_menu_comida', 60, function () {
            return MenuComida::whereActivo(1)->orderBy('orden')->get();
        });
    
        // 2. Obtener el menú específico
        $menu_comida = Cache::remember("menu_comida_$menu", 60, function () use ($menu) {
            return DB::table('menu_comida')
                ->whereActivo(1)
                ->orderBy('orden')
                ->where('titulo', '=', $menu)
                ->first();
        });
        abort_unless($menu_comida, Response::HTTP_NOT_FOUND);
    
        // 3. Obtener alimentos activos y disponibles para el menú
        $alimentos = Alimento::where('categoria', $menu)
            ->where('activo_hosteltactil', true)
            ->where('estado', 'Disponible')
            ->with(['combinados.subproductos', 'alergenos'])
            ->get();
    
        $alimentosConCombinados = [];
        foreach ($alimentos as $alimento) {
            // Obtener las combinaciones ordenadas (nivel 1)
            $combinaciones = $this->getOrderedCombinaciones($alimento->id);
    
            // Para cada combinación, obtener sus subproductos y subcombinaciones (nivel 2)
            foreach ($combinaciones as $combinacion) {
                $combinacion->subproductos = $this->getSubproductosWithSubcombinaciones($alimento->id, $combinacion->combinado_id);
            }
    
            $alimento->combinaciones = $combinaciones;
            $alimento->alergenoImg = $this->getAlergenosEncoded($alimento->id);
            $alimentosConCombinados[] = $alimento;
        }
    
        // Verificar si el pedido está permitido
        $this->verificarPedidoPermitido();
    
        return view('menu_single.index', compact('menu_comida', 'alimentos', 'categorias', 'alimentosConCombinados'));
    }
    
    /**
     * Obtiene las combinaciones (nivel 1) ordenadas para un alimento.
     * Se supone que ahora la tabla alimento_combinado tiene una columna "orden".
     */
    protected function getOrderedCombinaciones($alimentoId)
    {
        return Cache::remember("combinaciones_alimento_{$alimentoId}", 60, function () use ($alimentoId) {
            return DB::table('alimento_combinado')
                ->select(
                    'combinado.nombrecombi', 
                    'alimento_combinado.combinado_id', 
                    'alimento_combinado.alimento_id', 
                    'alimento_combinado.multiplicidad',
                    'alimento_combinado.orden',
                    'alimento_combinado.obligatorio'
                )
                ->join('combinado', 'combinado.id', '=', 'alimento_combinado.combinado_id')
                ->where('alimento_combinado.alimento_id', $alimentoId)
                ->orderBy('alimento_combinado.orden', 'asc')
                ->get();
        });
    }
    
    /**
     * Obtiene los subproductos (nivel 2) y para cada uno sus subcombinaciones.
     */
    protected function getSubproductosWithSubcombinaciones($alimentoId, $combinadoId)
    {
        // Obtener subproductos de la tabla combinado_subproducto, suponiendo que también tengan columna "orden" si fuera necesario
        $subproductos = Cache::remember("subproductos_combinacion_{$combinadoId}", 60, function () use ($combinadoId) {
            return DB::table('combinado_subproducto')
                ->join('subproductos', 'subproductos.id', '=', 'combinado_subproducto.subproducto_id')
                ->where('combinado_subproducto.combinado_id', $combinadoId)
                ->get();
        });
        $subproductosArray = $subproductos->toArray();
        $subCombiCollection = collect();
    
        foreach ($subproductosArray as $subproducto) {
            // Obtener subcombinaciones para cada subproducto
            $subCombi = DB::table('subproducto_combinado')
                ->join('combinado', 'combinado.id', '=', 'subproducto_combinado.combinado_id')
                ->join('alimento_combinado', 'alimento_combinado.combinado_id', '=', 'combinado.id')
                ->join('alimento', 'alimento.id', '=', 'subproducto_combinado.subproducto_id')
                ->join('subproductos', 'subproductos.id', '=', 'subproducto_combinado.subproducto_id')
                ->where('subproducto_combinado.padre_producto_id', $subproducto->id)
                ->select(
                    'subproducto_combinado.*',
                    'alimento.*',
                    'combinado.nombrecombi as subcategoria',
                    'subproducto_combinado.multiplicidad as multiplicidad',
                    'alimento_combinado.obligatorio',
                    'subproductos.precio'
                )
                ->get();
            $subCombiCollection = $subCombiCollection->merge($subCombi);
        }
    
        // Eliminar duplicados y asignar las subcombinaciones a cada subproducto
        $subCombiCollection = $subCombiCollection->unique('id');
        foreach ($subproductosArray as &$subproducto) {
            $subproducto = (object)$subproducto;
            $subproducto->subCombi = $subCombiCollection->toArray();
        }
    
        return $subproductosArray;
    }
    
    /**
     * Obtiene los alérgenos y codifica la imagen en base64.
     */
    protected function getAlergenosEncoded($alimentoId)
    {
        $alergenos = Cache::remember("alergenos_alimento_{$alimentoId}", 60, function () use ($alimentoId) {
            return DB::table('alimento_alergeno')
                ->select('alergeno.imagen')
                ->join('alergeno', 'alergeno.id', '=', 'alimento_alergeno.alergeno_id')
                ->where('alimento_alergeno.alimento_id', $alimentoId)
                ->get();
        });
        foreach ($alergenos as $alergeno) {
            $alergeno->imagen = base64_encode($alergeno->imagen);
        }
        return $alergenos;
    }
    
    function carta($menu)
    {
        $categorias = MenuComida::whereActivo(1)->get();

        $menu_comida = DB::table('menu_comida')
            ->whereActivo(1)
            ->where('titulo', '=', $menu)
            ->first();

        abort_unless($menu_comida, Response::HTTP_NOT_FOUND);

        $alimento = Alimento::query()
            ->where('categoria', '=', $menu)
            ->where('activo_hosteltactil', true)
            ->where('estado', 'Disponible')
            ->with('combinados.subproductos')
            ->get();

        return view('menu_single.carta', compact('menu_comida', 'alimento', 'categorias'));
    }
}
