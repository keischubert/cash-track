<?php
require fullPath("Repository.php");

$repository = new Repository();

//obteniendo los datos necesarios para que la vista se renderice correctamente.
$data = [
    "transactionTypes" => $repository->getAllTransactionTypes(),
    "moneyAccounts" => $repository->getAllMoneyAccounts(),
    "transactions" => $repository->getOrderedTransactions("date_time", "DESC", 13)
];

view('/home/home.view.php', $data);