<?php 

//extrayendo los valores obtenidos de home.php
$transactionTypes = $data["transactionTypes"];
$moneyAccounts = $data["moneyAccounts"];
$transactions = $data["transactions"];

ob_start(); //construyendo el partial

?>

<div class="row gx-3">
    <div class="col">
        <div class="w-75 mx-auto">
            <p class="text-center h5">Registrar una transacción</p>
            <form id="form" method="post">
                <div class="radioTransactionType d-flex justify-content-around my-4">
                <?php foreach($transactionTypes as $transactionType): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="transactionTypeId" id="radio<?=$transactionType['name']?>" value=<?=$transactionType['id']?> checked>
                        <label class="form-check-label" for=radio<?=$transactionType['name']?>"><?=$transactionType['name']?></label>
                    </div>
                <?php endforeach;?>
                </div>
                <div class="form-floating mb-3">
                    <input name="amount" id="amountInput"" type="number" pattern="\d*" class="form-control" placeholder="Monto">
                    <label for="amountInput" class="form-label">Monto</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="dateTime" id="dateInput" class="form-control" type="datetime-local"/>
                    <label for="dateInput" class="form-label">Fecha y hora</label>
                </div>
                <div class="form-floating mb-3">
                    <textarea style="resize: none;" name="description" id="descriptionInput" class="form-control" placeholder="Descripción"></textarea>
                    <label for="descriptionInput" class="form-label">Descripción</label>
                </div>
                <div class="form-floating mb-4">
                    <select name="moneyAccountId" id="moneyAccountSelect" class="form-select form-select-sm" aria-label="Floating label select example">
                        <option selected value="0">Seleccione una cuenta</option>
                        <?php foreach($moneyAccounts as $moneyAccount): ?>
                            <option value=<?= $moneyAccount["id"] ?>><?= $moneyAccount["name"] ?></option>
                        <?php endforeach ?>
                    </select>
                    <label for="moneyAccountSelect" class="form-label">Cuenta monetaria</label>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary px-4 py-2" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col">
        <p class="text-center h5 mb-3">Últimas transacciones</p>

        <table id="transaction-list" class="table w-75 mx-auto">
        <tbody>
            <?php foreach($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction["amount"] ?></td>
                    <td><?= $transaction["description"] ?></td>
                    <td>
                        <p><?= $transaction["transaction_type_id"] === 1 ? "Ingreso" : "Egreso" ?></p>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#form').submit(function(e){
        e.preventDefault();
        
        const data = new FormData(e.target);

        const transaction = {
            transactionTypeId: data.get("transactionTypeId"),
            amount: data.get("amount"),
            dateTime: data.get("dateTime"),
            description: data.get("description"),
            moneyAccountId: data.get("moneyAccountId")
        };

        //validacion del cliente
        if(transaction.amount <= 0){
            showMessage("El monto no puede ser menor o igual a cero", "alert-danger");
            return;
        }

        if(transaction.dateTime === ''){
            showMessage("La fecha es obligatoria", "alert-danger");
            return;
        }

        if(transaction.moneyAccountId == 0){
            showMessage("Debes seleccionar una cuenta", "alert-danger");
            return;
        }

         // Se envian los datos del form al endpoint POST /transactions
        $.ajax({
            url: '/transactions',
            type: 'POST',
            data: transaction,
            success: function(response){
                if(response.statusCode === 200){
                    showMessage(response.message, "alert-success");

                    //se anade la transaction a la lista de ultimos movimientos dinamicamente para no interactuar con el servidor
                    const transactionElement = $(`
                        <tr>
                            <td>${transaction["amount"]}</td>
                            <td>${transaction["description"]}</td>
                            <td>
                                <p>${transaction["transactionTypeId"] === 1 ? "Ingreso" : "Egreso"}</p>
                            </td>
                        </tr>
                    `);

                    $("#transaction-list tbody").prepend(transactionElement);

                    //reset de los campos del formulario
                    $("#form")[0].reset();
                }
                else if(response.statusCode === undefined){
                    location.href = `/error/404`;
                }
                else{
                    location.href = `/error/${response.statusCode}`;
                }
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
        $('#form').children("div").last().before(alert);

        // Hacer que desaparezca después de 3 segundos
        alert.delay(3000).fadeOut(500, function(){
            $(this).remove();
        });
    }
    });
</script>


<?php 

$content = [
    "title" => "CashTrack - Home",
    "content" => ob_get_clean()
];

view('layout.view.php', $content);