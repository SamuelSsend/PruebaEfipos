<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Alimento;
use App\Combinado;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoria = $request->get('categoria');
        $estado = $request->get('estado');
        $activo_hosteltactil = $request->get('activo_hosteltactil');
    
        $query = Alimento::with('combinados');
    
        if (!empty($search)) {
            $query->where('titulo', 'LIKE', '%' . $search . '%');
        }
        if (!empty($categoria)) {
            $query->where('categoria', $categoria);
        }
        if (!empty($estado)) {
            $query->where('estado', $estado);
        }
        if ($activo_hosteltactil !== null && $activo_hosteltactil !== '') {
            $query->where('activo_hosteltactil', $activo_hosteltactil);
        }
    
        $productos = $query->orderBy('id', 'desc')->paginate(10);
        
        $categorias = DB::table('menu_comida')->get();
    
        return view('productos.index', compact('productos', 'search', 'categorias'));
    }


    public function create()
    {
        $categorias = DB::table('menu_comida')->get();
        $combinados = Combinado::all();
        return view('productos.create', compact('categorias', 'combinados'));
    }

    public function store(Request $request)
    {
        $validator = $request->validate(
            [
            'titulo'=>'required|max:250',
            'precio'=>'required|max:250',
            'portada'=>'required|max:5000',
            ]
        );




            $alimento = new Alimento;
            $alimento->titulo = $request->get('titulo');
            $alimento->descripcion_manual = $request->get('descripcion_manual');
            $alimento->precio = $request->get('precio');
            $alimento->categoria = $request->get('categoria');
            $alimento->estado = 'Disponible';
            $alimento->oferta = '0';


        if($request->portada) {

            $extension2 = $request->portada[0]->extension();
            try{
                unlink(public_path('admin/'.$alimento->portada));
            }
            catch(\Exception $e){
            }
            if($extension2 == 'png' || $extension2 == 'jpeg' || $extension2 == 'jpg' || $extension2 == 'webp') {
                $imgname2 = uniqid();
                $imageName2 = $imgname2.'.'.$request->portada[0]->extension();
                $request->portada[0]->move(public_path('admin'), $imageName2);
                $alimento->portada = $imageName2;
            }else{
                Session::flash('danger', 'El formato de la imagen no se acepta');
                return redirect()->back();
            }
        }
            $alimento->save();

            $combinadosIds = $request->input('combinados');
            $alimento->combinados()->attach($combinadosIds);

            Session::flash('success', 'Se registró con exito el nuevo producto');
            return redirect()->route('admin.producto');

    }

    public function edit($id)
    {
        $categorias = DB::table('menu_comida')
        ->get();

        $combinados = Combinado::all();
        $producto = Alimento::findOrFail($id);
        $alergenos = DB::table('alergeno')->get();
        $checkAlergenos = DB::table('alimento_alergeno')
            ->select('alergeno_id')
            ->where('alimento_id', $id)
            ->get()
            ->pluck('alergeno_id')
            ->toArray();


        return view('productos.edit', compact('categorias', 'producto', 'combinados', 'alergenos', 'checkAlergenos'));
    }

    public function update(Request $request,$id)
    {
        DB::table('alimento_alergeno')->where('alimento_id', $id)->delete();
        if($request->has('alergenos')) {
            $alergenos = $request->get('alergenos');
            foreach($alergenos as $alergeno){
                DB::table('alimento_alergeno')->insert(
                    [
                    'alimento_id'=>$id,
                    'alergeno_id'=>$alergeno,
                    ]
                );
            }
        }

        $validator = $request->validate(
            [
            'titulo'=>'required|max:250',
            'precio'=>'required|max:250',
            'portada'=>'max:5000',
            ]
        );
        
        try {
            $alimento = Alimento::findOrFail($id);
            $alimento->titulo = $request->get('titulo');
            $alimento->descripcion_manual = $request->get('descripcion_manual') ?? '';
            $alimento->precio = $request->get('precio');
            $alimento->categoria = $request->get('categoria');
            $alimento->estado = 'Disponible';
            if($request->portada) {

                $extension2 = $request->portada[0]->extension();
                // try{
                //     unlink(public_path('admin/'.$alimento->portada));
                // }
                // catch(\Exception $e){
                // }
                if($extension2 == 'png' || $extension2 == 'jpeg' || $extension2 == 'jpg' || $extension2 == 'webp') {
                    $imgname2 = uniqid();
                    $imageName2 = $imgname2.'.'.$request->portada[0]->extension();
                    $request->portada[0]->move(public_path('admin'), $imageName2);
                    $alimento->portada = $imageName2;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }
            $alimento->update();
            
            Session::flash('success', 'Se actualizó con exito el nuevo producto');
            return redirect()->route('admin.producto');
        } catch (\Exception $e) {
            Session::flash('danger', $e->getMessage());
            return redirect()->back();
        }
    }

    public function eliminar($id)
    {
        try {
             $producto = Alimento::findOrFail($id);
            try{
                unlink(public_path('admin/'.$producto->portada));
            }
            catch(\Exception $e){
            }
             $producto->destroy($id);
             $producto->combinados()->detach();

             Session::flash('success', 'Se eliminó el producto correctamente');
             return redirect()->back();
        } catch (\Exception $e) {
             Session::flash('danger', $e);
             return redirect()->back();
        }
    }
    
    /**
     * Obtiene las combinaciones (nivel 1) ordenadas para un alimento.
     * Basado en tu menu_single().
     */
    protected function getOrderedCombinaciones($alimentoId)
    {
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
    }
    
    /**
     * Obtiene los subproductos (nivel 2) y sus subcombinaciones (sub-nivel), agrupando las
     * subcombinaciones por su subcategoria.
     */
    protected function getSubproductosWithSubcombinaciones($alimentoId, $combinadoId)
    {
        // 1. Obtener los subproductos del combinado
        $subproductos = DB::table('combinado_subproducto')
            ->join('subproductos', 'subproductos.id', '=', 'combinado_subproducto.subproducto_id')
            ->where('combinado_subproducto.combinado_id', $combinadoId)
            ->select(
                'combinado_subproducto.subproducto_id',
                'combinado_subproducto.combinado_id',
                'subproductos.nombre',
                'subproductos.precio'
            )
            ->get();
    
        $subproductosArray = $subproductos->toArray();
    
        // 2. Para cada subproducto, obtener y agrupar por subcategoria las “subcombinaciones”
        foreach ($subproductosArray as &$subproducto) {
            $subCombi = DB::table('subproducto_combinado')
                ->join('combinado', 'combinado.id', '=', 'subproducto_combinado.combinado_id')
                ->join('alimento_combinado', 'alimento_combinado.combinado_id', '=', 'combinado.id')
                ->join('alimento', 'alimento.id', '=', 'subproducto_combinado.subproducto_id')
                ->join('subproductos', 'subproductos.id', '=', 'subproducto_combinado.subproducto_id')
                ->where('subproducto_combinado.padre_producto_id', $subproducto->subproducto_id)
                ->orderBy('subproducto_combinado.orden', 'asc')
                ->select(
                    'subproducto_combinado.*',
                    'alimento.*',
                    'combinado.nombrecombi as subcategoria',
                    'subproducto_combinado.multiplicidad as multiplicidad',
                    'alimento_combinado.obligatorio as combi_obligatorio', // se podía usar, pero en update se usa la columna seccionobligatoria
                    'subproductos.precio'
                )
                ->get();
    
            $subproducto = (object)$subproducto;
            // Agrupar por subcategoria. Esto crea un arreglo con la llave de cada subcategoria.
            $subproducto->subCombi = $subCombi->unique('id')->groupBy('subcategoria')->toArray();
        }
    
        return $subproductosArray;
    }
    
    
    public function editCombinados($id)
    {
        // 1. Cargar el alimento
        $alimento = Alimento::findOrFail($id);
    
        // 2. Obtener las combinaciones (nivel 1) ordenadas
        $combinaciones = DB::table('alimento_combinado')
                ->select(
                    'combinado.nombrecombi', 
                    'alimento_combinado.combinado_id', 
                    'alimento_combinado.alimento_id', 
                    'alimento_combinado.multiplicidad',
                    'alimento_combinado.orden',
                    'alimento_combinado.obligatorio'
                )
                ->join('combinado', 'combinado.id', '=', 'alimento_combinado.combinado_id')
                ->where('alimento_combinado.alimento_id', $id)
                ->orderBy('alimento_combinado.orden', 'asc')
                ->get();
    
        // 3. Para cada combinación, obtener los subproductos y subcombinaciones
        foreach ($combinaciones as $comb) {
            $comb->subproductos = $this->getSubproductosWithSubcombinaciones($id, $comb->combinado_id);
        }
    
        return view('productos.edit_combinados', compact('alimento', 'combinaciones'));
    }
    
    
    public function updateCombinados(Request $request, $id)
    {
        $alimento = Alimento::findOrFail($id);
    
        // Actualizar combinaciones (Nivel 1)
        if ($request->has('combinado_orden')) {
            foreach ($request->combinado_orden as $combinadoId => $nuevoOrden) {
                DB::table('alimento_combinado')
                    ->where('alimento_id', $id)
                    ->where('combinado_id', $combinadoId)
                    ->update([
                        'orden'       => $nuevoOrden,
                        'obligatorio' => isset($request->combinado_obligatorio[$combinadoId]) ? 1 : 0
                    ]);
            }
        }
    
        // Actualizar grupos de subcombinaciones (Nivel 3)
        // Se envía subcombi_orden[<subproducto_id>][<subcategoria>] y subcombi_obligatorio[<subproducto_id>][<subcategoria>]
        if ($request->has('subcombi_orden')) {
            foreach ($request->subcombi_orden as $subproductoId => $grupos) {
                foreach ($grupos as $subcategoria => $nuevoOrden) {
                    DB::table('subproducto_combinado')
                        ->where('padre_producto_id', $subproductoId)
                        ->whereIn('combinado_id', function($query) use ($subcategoria) {
                            $query->select('id')
                                  ->from('combinado')
                                  ->where('nombrecombi', $subcategoria);
                        })
                        ->update(['orden' => $nuevoOrden]);
                }
            }
        }
    
        if ($request->has('subcombi_obligatorio')) {
            foreach ($request->subcombi_obligatorio as $subproductoId => $grupos) {
                foreach ($grupos as $subcategoria => $val) {
                    $obligatorio = $val ? 1 : 0;
                    DB::table('subproducto_combinado')
                        ->where('padre_producto_id', $subproductoId)
                        ->whereIn('combinado_id', function($query) use ($subcategoria) {
                            $query->select('id')
                                  ->from('combinado')
                                  ->where('nombrecombi', $subcategoria);
                        })
                        ->update(['seccionobligatoria' => $obligatorio]);
                }
            }
        }
    
        return redirect()
            ->route('admin.producto')
            ->with('success', 'Combinados actualizados correctamente.');
    }


    
    public function edit_combinados($id)
    {
        $producto = Alimento::findOrFail($id);
        $combinados = Combinado::all();

        return view('productos.edit_combina2', compact('producto', 'combinados'));
    }

    public function update_combinados(Request $request, Alimento $producto)
    {
        $combinado = $request->input('combinado_id');

        if ($producto->combinados->contains('id', $combinado)) {
            return redirect()->back()->with('error', 'El combinado ya está relacionado con el producto');
        }

        $producto->combinados()->attach($combinado);

        return redirect()->back()->with('message', 'Combinado relacionado correctamente');
    }

    public function eliminar_combinados(Alimento $producto, Combinado $combinado)
    {
        $producto->combinados()->detach($combinado->id);
        return redirect()->back()->with('message', 'Subproducto eliminado correctamente');
    }


    public function estado($id)
    {
        try {
             $producto = Alimento::findOrFail($id);
            if($producto->estado == 'Disponible') {
                $producto->estado = 'Agotado';
            }else{
                $producto->estado = 'Disponible';
            }
             $producto->update();

             Session::flash('success', 'Se actualizó el estado del producto correctamente');
             return redirect()->back();
        } catch (\Exception $e) {
             Session::flash('danger', $e);
             return redirect()->back();
        }
    }

    public function oferta($id)
    {
        try {
             $producto = Alimento::findOrFail($id);
            if($producto->oferta == '0') {
                $producto->oferta = '1';
            }else{
                $producto->oferta = '0';
            }
             $producto->update();

             Session::flash('success', 'Se modificó el estado del producto');
             return redirect()->back();
        } catch (\Exception $e) {
             Session::flash('danger', $e);
             return redirect()->back();
        }
    }

    public function index_oferta()
    {
        $productos = Alimento::query()
            ->where('oferta', '=', '1')
            ->orderby('id', 'desc')
            ->get();

        return view('productos.index_oferta', compact('productos'));
    }

    public function update_portada(Request $request, $id)
    {
        $validator = $request->validate(
            [
            'portada_oferta'=>'max:5000',
            ]
        );
        try {

            $alimento = Alimento::findOrFail($id);
            if($request->portada_oferta) {

                $extension2 = $request->portada_oferta[0]->extension();
                try{
                    unlink(public_path('admin/'.$alimento->portada_oferta));
                }
                catch(\Exception $e){
                }
                if($extension2 == 'png' || $extension2 == 'jpeg' || $extension2 == 'jpg' || $extension2 == 'webp') {
                    $imgname2 = uniqid();
                    $imageName2 = $imgname2.'.'.$request->portada_oferta[0]->extension();
                    $request->portada_oferta[0]->move(public_path('admin'), $imageName2);
                    $alimento->portada_oferta = $imageName2;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }
            $alimento->update();
            Session::flash('success', 'Se actualizó con exito el nuevo producto');
            return redirect()->route('index_oferta.producto');
        } catch (\Exception $e) {
            Session::flash('danger', $e);
            return redirect()->back();
        }
    }
    public function updateCombinadosOrder(Request $request, $productoId)
    {
        $order = $request->input('order');
        foreach ($order as $position => $combinadoId) {
            DB::table('alimento_combinado')
                ->where('alimento_id', $productoId)
                ->where('combinado_id', $combinadoId)
                ->update(['orden' => $position]);
        }
        return response()->json(['success' => true]);
    }

    public function updateCombinadoObligatorio(Request $request)
    {
        $productoId = $request->input('producto_id');
        $combinadoId = $request->input('combinado_id');
        $obligatorio = $request->input('obligatorio');
    
        // Actualiza el registro en la tabla alimento_combinado
        DB::table('alimento_combinado')
          ->where('alimento_id', $productoId)
          ->where('combinado_id', $combinadoId)
          ->update(['obligatorio' => $obligatorio]);
    
        return response()->json(['success' => true]);
    }

    public function showHierarchy($alimentoId)
    {
        $alimento = Alimento::with([
            'combinadosNivel1.subcombinados.subproducto',
            'combinadosNivel1.subcombinados' => function($query) {
                $query->orderBy('orden');
            }
        ])->findOrFail($alimentoId);
    
        return view('productos.hierarchy', compact('alimento'));
    }

}
