<?php

namespace App\Console\Commands;

use App\Alimento;
use App\Combinado;
use App\ConfigGeneral;
use App\HostelTactil\Carta;
use App\MenuComida;
use App\Subproducto;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SincronizarCarta extends Command
{
    protected $signature = 'efipos:sync-carta';
    protected $description = 'Sincroniza la carta';

    private $combinadosId = [];
    private $alimentosActivadosId = [];
    private $subProductosActivadosId = [];

    public function handle()
    {
        $carta = Carta::get();
        Log::info("Iniciando el procesamiento de categorías.");
        $this->processCategorias($carta['categorias']);
        Log::info("Procesamiento de categorías completado.");

        Log::info("Iniciando el procesamiento de secciones.");
        $this->processSecciones($carta['secciones']);
        Log::info("Procesamiento de secciones completado."); 
    
        Log::info("Iniciando el procesamiento de multiplicidades.");
        $this->processMultiplicidad($carta['categorias'], $carta['secciones']);
        Log::info("Procesamiento de multiplicidades completado.");

        // Desactivar los alimentos y subproductos que no se sincronizaron
        Alimento::whereNotIn('id', $this->alimentosActivadosId)->update(['estado' => 'Baja', 'activo_hosteltactil' => false]);
        Subproducto::whereNotIn('id', $this->subProductosActivadosId)->update(['estado' => 'Baja', 'activo_hosteltactil' => false]);

        // Eliminar los combinados que no se sincronizaron
        DB::table('combinado_subproducto')->whereNotIn('combinado_id', $this->combinadosId)->delete();
        DB::table('alimento_combinado')->whereNotIn('combinado_id', $this->combinadosId)->delete();
        // Combinado::whereNotIn('id', $this->combinadosId)->delete();


        file_put_contents(storage_path('hosteltactil_ultima_sync.txt'), date('d/m/Y H:i:s'));
        Log::info("Sincronización completada. Archivo de sincronización actualizado.");
    }

    protected function processMultiplicidad(array $categorias, array $secciones)
    {
        foreach ($categorias as $categoria) {
            foreach ($categoria['productos'] as $producto) {
                $this->processProducto($producto, $secciones);
            }
        }
    }

    protected function processProducto($producto, $secciones)
    {
        if (isset($producto['menusecciones']) && is_array($producto['menusecciones'])) {
            foreach ($producto['menusecciones'] as $menuseccion) {
                // Procesar multiplicidad (por defecto 1)
                $multiplicidad = $menuseccion['multiplicidad'] ?? 1;
                
                DB::table('alimento_combinado')->updateOrInsert(
                    ['alimento_id' => $producto['id'], 'combinado_id' => $menuseccion['id']],
                    ['multiplicidad' => $multiplicidad]
                );
        
                // Obtener la sección (subcategoría) usando el idseccionmenu
                $seccion = array_filter($secciones, fn($sec) => $sec['id'] == $menuseccion['idseccionmenu']);
                
                if (!empty($seccion)) {
                    $sec = array_values($seccion)[0];
                    foreach ($sec['productos'] as $prod) {
                        // Insertar o actualizar en subproducto_combinado
                        DB::table('subproducto_combinado')->updateOrInsert(
                            [
                                'padre_producto_id'  => $producto['id'],
                                'combinado_id'       => $sec['id'],
                                'subproducto_id'     => $prod['id'],
                            ],
                            [
                                'nombre_subcategoria' => $sec['nombre'], // Nombre de la subcategoría
                                'multiplicidad'       => $multiplicidad,   // Multiplicidad definida en menuseccion
                                'updated_at'          => now(),
                            ]
                        );
        
                        if (isset($prod['subproductos'])) {
                            $this->processSubProductos($prod, $secciones);
                        }
                    }
                }
            }
        }
    }

    protected function processSubProductos($producto, $secciones)
    {
        if (isset($producto['menusecciones']) && is_array($producto['menusecciones'])) {
            foreach ($producto['menusecciones'] as $menuseccion) {
                $seccion = array_filter(
                    $secciones, 
                    fn($sec) => $sec['id'] == $menuseccion['idseccionmenu']
                );
        
                if (!empty($seccion)) {
                    $sec = array_values($seccion)[0];
                    foreach ($sec['productos'] as $prod) {
                        DB::table('subproducto_combinado')->updateOrInsert(
                            [
                                'padre_producto_id'  => $producto['id'],
                                'combinado_id'       => $sec['id'],
                                'subproducto_id'     => $prod['id'],
                            ],
                            [
                                'nombre_subcategoria' => $sec['nombre'],
                                'multiplicidad'       => $menuseccion['multiplicidad'] ?? 1,
                                'updated_at'          => now(),
                            ]
                        );
        
                        if (isset($prod['subproductos']) && is_array($prod['subproductos'])) {
                            $this->processSubProductos($prod, $secciones);
                        }
                    }
                } else {
                    Log::warning("No se encontró la sección para el subproducto ID: " . $producto['id']);
                }
            }
        } else {
            Log::warning("No se encontraron menusecciones para el subproducto ID: " . $producto['id']);
        }
    }

    //antiguo codigo

    protected function processCategorias(array $categorias)
    {
        collect($categorias)->each(
            function ($categoriaArray) {
                $categoria = $this->createOrUpdateCategoria($categoriaArray);

                $this->createOrUpdateAlimentos($categoriaArray['productos'], $categoria);
            }
        );
    }

    protected function createOrUpdateCategoria(array $data): MenuComida
    {
        return MenuComida::updateOrCreate(
            ['id' => $data['id']],
            ['titulo' => $data['nombre'], 'activo_hosteltactil' => 1]
        );
    }

    protected function createOrUpdateAlimentos(array $productos, MenuComida $categoria)
    {
        collect($productos)
            ->filter(
                function ($productoArray) {
                    return $productoArray['nombre'] != '';
                }
            )
            ->each(
                function ($productoArray) use ($categoria) {
                    $alimento = $this->createOrUpdateAlimento($productoArray, $categoria);
                    $this->alimentosActivadosId[] = $alimento->id;
                }
            );
    }

    protected function createOrUpdateAlimento(array $data, MenuComida $categoria): Alimento
    {
        $config = ConfigGeneral::first();
        
        // Intentamos obtener el alimento existente
        $alimento = Alimento::firstOrNew(['id' => $data['id']]);
        
        $values = [
            'categoria_id' => $categoria->id,
            'categoria' => $categoria->titulo,
            'titulo' => $data['nombre'],
            'descripcion_hosteltactil' => $data['descripcion'],
            'precio' => $data['tarifa' . ($config->hosteltactil_tarifa ?? 'default_tarifa')],
            'activo_hosteltactil' => $data['activo'],
        ];
    
        // Solo asignamos valores predeterminados si es un nuevo alimento
        if (!$alimento->exists) {
            $values['estado'] = 'Disponible';
            $values['portada'] = "";
            $values['oferta'] = 0;
            $values['descripcion_manual'] = '';
        } else {
            // Si el alimento ya existe, conservamos la portada existente en caso de que haya una
            $values['portada'] = $alimento->portada ?? "";
        }
    
        // Guardamos los valores
        $alimento->fill($values)->save();
    
        // Añadir el ID del alimento a la lista de activados
            $this->alimentosActivadosId[] = $alimento->id;
    
        // Sincronizar menusecciones sin borrar las existentes
        if (!empty($data['menusecciones'])) {
            collect($data['menusecciones'])->each(
                fn($menuSeccionArray) => $alimento->combinados()->syncWithoutDetaching($menuSeccionArray['idseccionmenu'])
            );
        }
    
        return $alimento;
    }

    protected function processSecciones(array $secciones)
    {
        collect($secciones)->each(
            function ($seccionArray) {
                $combinado = $this->createOrUpdateCombinado($seccionArray);
                $subproductos = $this->createOrUpdateSubproductos($seccionArray['productos']);

                $combinado->subproductos()->sync($subproductos->pluck('id'));

                $this->combinadosId[] = $combinado->id;
            }
        );
    }

    protected function createOrUpdateCombinado(array $data): Combinado
    {
        return Combinado::updateOrCreate(
            ['id' => $data['id']],
            ['nombrecombi' => $data['nombre']]
        );
    }

    protected function createOrUpdateSubproductos(array $productos): Collection
    {
        return collect($productos)->map(
            function ($productoArray) {
                $subproducto = $this->createOrUpdateSubproducto($productoArray);
                $this->subProductosActivadosId[] = $subproducto->id;

                return $subproducto;
            }
        );
    }

    protected function createOrUpdateSubproducto(array $data): Subproducto
    {
        $subproducto = Subproducto::firstOrNew(['id' => $data['id']]);
        $titulo = Alimento::where('id', $data['id'])->value('titulo');
        //         Log::info($titulo);
        $values = [
            'precio' => $data['suplemento'],
            'nombre' => $titulo,
        ];

        if (!$subproducto->exists) {
            $values['estado'] = 'Baja';
        }

        $subproducto->fill($values)->save();

        return $subproducto;
    }
}