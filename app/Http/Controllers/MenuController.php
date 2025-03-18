<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MenuComida;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Str;
use App\Galeria;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $menu = DB::table('menu_comida')
            ->orderby('orden')
            ->get();

        return view('configuraciones.menu.index', compact('menu'));
    }

    public function create()
    {
        return view('configuraciones.menu.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate(
            [
            'titulo'=>'required|max:50|unique:menu_comida',
            'enlace'=>'required|max:300',
            'preview'=>'required|max:5000',
            'fondo'=>'required|max:5000',
            ]
        );

        try {
            $menu_comida = new MenuComida;
            $menu_comida->titulo = $request->get('titulo');
            $menu_comida->enlace = route('inicio').'/menu/'.Str::slug($request->get('titulo'), '_');
            if($request->preview) {

                $extension2 = $request->preview[0]->extension();
                if($extension2 == 'png' || $extension2 == 'jpeg' || $extension2 == 'jpg' || $extension2 == 'webp' || $extension2 == 'svg') {
                    $imgname2 = uniqid();
                    $imageName2 = $imgname2.'.'.$request->preview[0]->extension();
                    $request->preview[0]->move(public_path('admin'), $imageName2);
                    $menu_comida->preview = $imageName2;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }
            if($request->fondo) {

                $extension3 = $request->fondo[0]->extension();
                if($extension3 == 'png' || $extension3 == 'jpeg' || $extension3 == 'jpg' || $extension3 == 'webp' || $extension3 == 'svg') {
                    $imgname3 = uniqid();
                    $imageName3 = $imgname3.'.'.$request->fondo[0]->extension();
                    $request->fondo[0]->move(public_path('admin'), $imageName3);
                    $menu_comida->fondo = $imageName3;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }

            $menu_comida->save();
            Session::flash('success', 'Se registró con exito el nuevo menú');
            return redirect()->route('index.menu');
        } catch (\Exception $e) {
            Session::flash('danger', $e);
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $menu = MenuComida::findOrFail($id);
        $photos = Galeria::all(); // Obtener todas las imágenes de la galería
        return view('configuraciones.menu.edit', compact('menu', 'photos'));
    }

    public function update(Request $request,$id)
    {

        $validator = $request->validate(
            [
            'titulo'=>'required',
            'enlace'=>'required|max:300',
            'preview'=>'max:5000',
            'fondo'=>'max:5000',
            'activo'=>'required|boolean',
            ]
        );



        try {
            $menu_comida = MenuComida::findOrFail($id);
            $menu_comida->titulo = $request->get('titulo');
            $menu_comida->activo = $request->get('activo');
            $menu_comida->enlace = route('inicio').'/menu/'.Str::slug($request->get('titulo'), '_');
            if($request->preview) {
                try{
                    unlink(public_path('admin/'.$menu_comida->preview));
                }
                catch(\Exception $e){
                }
                $extension2 = $request->preview[0]->extension();
                if($extension2 == 'png' || $extension2 == 'jpeg' || $extension2 == 'jpg' || $extension2 == 'webp' || $extension2 == 'svg') {
                    $imgname2 = uniqid();
                    $imageName2 = $imgname2.'.'.$request->preview[0]->extension();
                    $request->preview[0]->move(public_path('admin'), $imageName2);
                    $menu_comida->preview = $imageName2;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }
            if($request->fondo) {
                try{
                    unlink(public_path('admin/'.$menu_comida->fondo));
                }
                catch(\Exception $e){
                }
                $extension3 = $request->fondo[0]->extension();
                if($extension3 == 'png' || $extension3 == 'jpeg' || $extension3 == 'jpg' || $extension3 == 'webp' || $extension3 == 'svg') {
                    $imgname3 = uniqid();
                    $imageName3 = $imgname3.'.'.$request->fondo[0]->extension();
                    $request->fondo[0]->move(public_path('admin'), $imageName3);
                    $menu_comida->fondo = $imageName3;
                }else{
                    Session::flash('danger', 'El formato de la imagen no se acepta');
                    return redirect()->back();
                }
            }

            $menu_comida->update();
            Session::flash('success', 'Se actualizó con exito el nuevo menú');
            return redirect()->route('index.menu');
        } catch (\Exception $e) {
            Session::flash('danger', $e);
            return redirect()->back();
        }
    }

    public function destroy($id)
    {
        try {
            $menu = MenuComida::findOrFail($id);
            try{
                unlink(public_path('admin/'.$menu->preview));
                unlink(public_path('admin/'.$menu->fondo));
            }
            catch(\Exception $e){
            }
            $menu->destroy($id);

            Session::flash('success', 'Se eliminó el menú correctamente');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('danger', $e);
            return redirect()->back();
        }
    }
    
    public function updateOrderActive(Request $request)
    {
        $order = $request->input('order'); // IDs en nuevo orden
        foreach ($order as $position => $id) {
            MenuComida::where('id', $id)->update(['orden' => $position]);
        }
        return response()->json(['success' => true]);
    }
    
    public function updateOrderInactive(Request $request)
    {
        $order = $request->input('order'); // IDs en nuevo orden
        foreach ($order as $position => $id) {
            MenuComida::where('id', $id)->update(['orden' => $position]);
        }
        return response()->json(['success' => true]);
    }

    public function selectPhoto(Request $request)
    {
        $photoId = $request->input('photo');
        $photo = Galeria::find($photoId);
    
        if ($photo) {
            // Realizar la acción necesaria con la foto seleccionada
            // Por ejemplo, actualizar el menú con la imagen seleccionada
            $menuId = $request->input('menu_id');
            $menu = MenuComida::findOrFail($menuId);
            $menu->preview = $photo->foto; // Asignar la imagen de la galería como preview
            $menu->save();
        }
    
        return back()->with('success', 'Foto seleccionada con éxito.');
    }

}
