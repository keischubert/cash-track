<?php

$transactions = $data["transactions"];

ob_start(); //construccion del partial

?>

<div class="w-75 mx-auto">
    <p class="text-center h5 mb-3">Historial de transacciones</p>

    <div class="container mt-4">
        <form class="d-flex" role="search">
            <input class="form-control" type="search" placeholder="Buscar por monto, descripcion, tipo de cuenta, transaccion o fecha..." name="inputSearch">
        </form>
    </div>

    <table id="transaction-list" class="table">
        <tbody >
            <?php foreach($transactions as $transaction): ?>
                <tr>
                    <td><?= $transaction["amount"] ?></td>
                    <td><?= $transaction["description"] ?></td>
                    <td><?= $transaction["moneyAccountName"] ?></td>
                    <td>
                        <p><?= $transaction["transactionTypeName"] ?></p>
                        <p><?= $transaction["date_time"] ?></p>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $("form input[name='inputSearch']").on("input", function(e) {
            
            let input = $(this).val();

            //ejecutar ajax con /transactions/search?name=10000 example.
            $.ajax({
                url: `/transactions?search=${input}`,
                type: 'GET',
                success: function(response){
                    $("#transaction-list tbody").empty();

                    if(response.transactions.length > 0){
                        response.transactions.forEach(transaction => {
                        const row = $(`
                            <tr>
                                <td>${transaction.amount}</td>
                                <td>${transaction.description}</td>
                                <td>${transaction.moneyAccountName}</td>
                                <td>
                                    <p>${transaction.transactionTypeName}</p>
                                    <p>${transaction.date_time}</p>
                                </td>
                            </tr>
                        `);

                        $("#transaction-list tbody").append(row);
                    });
                    }
                },
                error: function(){
                    location.href = "/error/500";
                }
            });

        });
    });

</script>

<?php

$content = [
    "title" => "CashTrack - Transacciones",
    "content" => ob_get_clean()
];

view("layout.view.php", $content);