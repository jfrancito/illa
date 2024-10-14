<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Cliente;
use App\Modelos\Categoria;
use App\Modelos\Precotizacion;
use App\Modelos\Archivo;
use App\Modelos\Cotizacion;
use App\Modelos\DetalleCotizacion;
use App\Modelos\DetalleCotizacionAnalisis;
use App\Modelos\Requerimiento;

use App\User;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\CotizacionTraits;
use App\Traits\ConfiguracionTraits;
use Mail;

class PruebasController extends Controller
{
    //
    use GeneralesTraits;
    use CotizacionTraits;
    use ConfiguracionTraits;
    
    public function actionPruebaEmail($emailfrom,$nombreusuario)
    {
        $datos = compact('emailfrom','nombreusuario');
        try {
            Mail::send('emails.emailprueba',$datos,function ($message) use ($emailfrom,$nombreusuario){
                $message->to('neil.vigil@induamerica.com.pe','Neil Vigil')
                    ->from($emailfrom,$nombreusuario)
                    ->subject('Prueba Email');
            });
        } catch (Exception $ex) {
            dd($ex);
        }
        dd($datos);

        // from($emailfrom,$nombreusuario)->to('neil8928@gmail.com')->send();
    }

}
