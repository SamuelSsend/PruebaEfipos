<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmación de Compra</title>
    <style type="text/css">
        /* Estilos inline para mejorar la compatibilidad */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .content {
            margin: 20px 0;
        }
        .order-details, .coupon-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .order-details th, .order-details td,
        .coupon-details th, .coupon-details td {
            padding: 8px;
            border: 1px solid #dddddd;
            text-align: left;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #dddddd;
            padding-top: 15px;
            margin-top: 15px;
        }
        a {
            color: #3f51b5;
            text-decoration: none;
        }
        /* Elimina bullet points de las listas y ajusta márgenes */
        ul {
            list-style-type: none;
        }
        /* Si deseas separar cada ítem de observaciones */
        .observaciones {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f3fe;
            border: 1px solid #b3d4fc;
            color: #31708f;
        }
    </style>
</head>
<body>
    <div class="container">
       <!-- Encabezado con logo y título -->
       <div class="header">
           <h2>Confirmación de Compra</h2>
       </div>
       
       <div class="content">
           <p>Hola {{ $customerName }},</p>
           <p>Gracias por tu compra. Tu pedido número <strong>{{ $orderNumber }}</strong> se ha generado con éxito.</p>
           
           <h3>Detalles del Pedido</h3>
           <table class="order-details">
              <tr>
                 <th>Producto</th>
                 <th>Cantidad</th>
                 <th>Precio Unitario</th>
              </tr>
              @foreach ($orderItems as $item)
                <tr>
                   <td>
                     {{ $item['nombre'] }}
                     @if (!empty($item['comentarioscocina']))
                        <br>
                         <small>
                           @foreach ($item['comentarioscocina'] as $comentario)
                              Comentario: {{ $comentario['descripcion'] }}<br>
                           @endforeach
                         </small>
                     @endif
                     @if (!empty($item['subproductos']))
                        <ul>
                        @foreach ($item['subproductos'] as $subitem)
                           <li>{{ $subitem['nombre'] }} - Precio: {{ $subitem['preciounitario'] }}€</li>
                           @if(!empty($subitem['Subproductos']))
                               <ul>
                                  @foreach($subitem['Subproductos'] as $segundoNivel)
                                     <li>{{ $segundoNivel['nombre'] }} - Precio: {{ $segundoNivel['preciounitario'] }}€</li>
                                  @endforeach
                               </ul>
                           @endif
                        @endforeach
                        </ul>
                     @endif
                   </td>
                   <td>{{ $item['cantidad'] }}</td>
                   <td>{{ $item['preciounitario'] }}€</td>
                </tr>
              @endforeach
           </table>
           
           @if(count($cupones) > 0)
             <h3>Cupones Aplicados</h3>
             <table class="coupon-details">
               @foreach($cupones as $cupon)
                 <tr>
                   @if($cupon->tipo_descuento === 'envio_gratis')
                     <td>Cupon: {{ $cupon->nombre }}</td>
                     <td>Envío Gratis</td>
                   @else
                     <td>Cupon: {{ $cupon->nombre }}</td>
                     <td>Descuento: {{ $cupon->descuento_aplicado }}€</td>
                   @endif
                 </tr>
               @endforeach
             </table>
           @endif
           
           <p><strong>Total pagado:</strong> {{ $totalPaid }}€</p>
           
           @if($deliveryAddress !== "RECOGIDA")
             <p><strong>Dirección de entrega:</strong> {{ $deliveryAddress }}</p>
           @else
             <p><strong>Recoger en restaurante</strong></p>
           @endif
           
           <!-- Sección de Observaciones (opcional) -->
           @if($observaciones)
             <div class="observaciones">
                 <h4>Observaciones</h4>
                 <p>{!! $observaciones !!}</p>
             </div>
           @endif

           <p>Puedes visitar nuestro sitio web haciendo <a href="{{ $appUrl }}">clic aquí</a>.</p>
       </div>
       
       <div class="footer">
           <p>Este es un correo electrónico automatizado. Por favor, no responda a este mensaje.</p>
           <p>Gracias por confiar en nosotros.</p>
       </div>
    </div>
</body>
</html>