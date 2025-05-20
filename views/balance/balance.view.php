<?php

ob_start(); //construyendo el partial 

?>

<div class="card shadow w-50 mx-auto">
  <div class="card-body">
    <h5 class="card-title text-center">Resumen</h5>
    <ul class="list-group list-group-flush mb-3" id="balance-content">
      <li class="list-group-item d-flex justify-content-between row">
          <span class="w-50">Fecha inicio:</span>
          <input name="dateTime" id="dateStartInput" class="form-control w-50" type="date"/>
      </li>
      <li class="list-group-item d-flex justify-content-between row">
          <span class="w-50">Fecha Fin:</span>
          <input name="dateTime" id="dateEndInput" class="form-control w-50" type="date"/>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span>Ingresos:</span>
        <span id="income-amount">0 Gs</span>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span>Egresos:</span>
        <span id="expense-amount">0 Gs</span>
      </li>
      <li class="list-group-item d-flex justify-content-between">
        <span>Balance:</span>
        <span id="balance-amount">0 Gs</span>
      </li>
    </ul>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){

      $("#balance-content").change(function(e) {

        //validacion cliente: fechas vacias
        if($("#dateStartInput").val() === "" || $("#dateEndInput").val() === ""){
          return;
        }

        //validacion cliente: fecha fin mayor a fecha inicio
          const dateStart = new Date($("#dateStartInput").val());
          const dateEnd = new Date($("#dateEndInput").val());
          if(dateStart > dateEnd){
            showMessage("Fecha de inicio no puede ser mayor a fecha fin", "alert-danger");
            return;
          }

        //ejecucion de ajax si se pasa la validacion del cliente.  
        $.ajax({
          url: `/balance?dateStart=${$("#dateStartInput").val()}&dateEnd=${$("#dateEndInput").val()}`, //ejecutando la ruta /balance con query parameters.
          type: 'GET',
          success: function(response){

            //modificando dinamicamente los valores del dom a partir de la respuesta del server.
            $("#income-amount").text(`${parseInt(response.income)} Gs`);
            $("#expense-amount").text(`${parseInt(response.expense)} Gs`);
            $("#balance-amount").text(`${parseInt(response.income - response.expense)} Gs`);
          },
          error: function(){
              location.href = "/error/500";
          }
      });
      });
      
      //helpers
      const showMessage = (msg, type = "alert alert-success") => {
        const alert = $(`<div class='alert ${type} mb-3'>${msg}</div>`);
        
        // Insertarlo antes del boton del formulario
        $('#balance-content').after(alert);

        // Hacer que desaparezca después de 3 segundos
        alert.delay(3000).fadeOut(500, function(){
            $(this).remove();
        });
      };
    });
</script>

<?php

view("layout.view.php", ["content" => ob_get_clean(), "title" => "CashTrack - Balance"]);