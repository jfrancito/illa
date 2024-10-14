
<nav class="navbar navbar-default navbar-fixed-top be-top-header {{Session::get('color_meta')}}">
  <div class="container-fluid">
    <div class="navbar-header"> 
      <div class="color_rosa"><b>SISTEMA DE GESTION DE COMPRA Y VENTA</b></div>
    </div>

    <div class="be-right-navbar {{Session::get('color_meta')}}">
      <ul class="nav navbar-nav navbar-right be-user-nav">
        <li><div class="page-title"><span></span></div></li>

        <li class="dropdown">
          <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle">
            <img src="{{ asset('public/img/avatar.png') }}" alt="Avatar">
            <span class="user-name color_rosa" style="font-size: 18px !important;"> <b>{{Session::get('usuario')->nombre}}</b></span></a>
          <ul role="menu" class="dropdown-menu">
            <li>
              <div class="user-info color_dorado" >
                <div class="user-name"> {{Session::get('usuario')->nombre}}</div>
                <div class="user-position online">disponible</div>
              </div>
            </li>
            <li><a href="{{ url('/cerrarsession') }}"><span class="icon mdi mdi-power"></span>Cerrar sesión</a></li>
          </ul>
        </li>
      </ul>
    </div>

  </div>
</nav>