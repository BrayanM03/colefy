function iniciarSesion(){
    toggleLoader()

    user = document.getElementById("user").value;
    pass = document.getElementById("pass").value;

    let validacion = validarDatos(user, pass);
    if(validacion){

        var datosSesion = new FormData();
 
        datosSesion.append("username", user);
        datosSesion.append("pass", pass); // number 123456 is immediately converted 
    
    $.ajax({
        type: "POST",
        url: BASE_URL + "servidor/database/iniciar-sesion.php",
        processData: false,
        contentType: false,
        data: datosSesion,
        dataType: "JSON",
        success: function (response) {
            if(response.estado == 2){
                toggleLoader()
                animarError('user', 2);
            }else if(response.estado == 3){
                toggleLoader()
                animarError('pass', 2);
            }else if(response.estado == 1){
                localStorage.setItem('rol', response.rol);
                localStorage.setItem('sucursal', response.sucursal);
                window.location.href = BASE_URL + 'dashboard'
            } 

        }
    });
    }else{
        toggleLoader()
    }

   

}


function validarDatos(user, password) {

    if(user == null || user == undefined || user.length == 0 || user == ""){
        animarError("user", 1)
        return false;
    }

    if(password == null || password == undefined || password.length == 0 || password == ""){
        animarError("pass", 1)
        return false;
    }

    return true;
    
}

function animarError(id_element, type){
    document.getElementById(id_element).classList.add("animate__headShake");
    setTimeout(function() {
        document.getElementById(id_element).classList.remove("animate__headShake");
    }, 700);

    if(id_element == "user" && type == 1) {
        Toast.fire({
            icon: 'error',
            title: 'Ingresa un usuario'
          })
    }else if(id_element == "pass" && type == 1){
        Toast.fire({
            icon: 'error',
            title: 'Ingresa una contraseña'
          })
    }

    if(id_element == "user" && type == 2){
        Toast.fire({
            icon: 'error',
            title: 'El usuario no existe'
          })
    }else if(id_element == "pass" && type == 2){
        Toast.fire({
            icon: 'error',
            title: 'Contraseña incorrecta'
          })
    }
    
}

const Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
  
  