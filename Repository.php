<?php
declare(strict_types=1);

class Repository {
    protected array $config;
    protected PDO $pdo;

    public function __construct() {
        $this->config = require fullPath("config.php");
        $this->pdo = new PDO($this->config["dsn"], $this->config["user"], $this->config["password"]);
    }

    public function getAllMoneyAccounts(): array{
        $query = "SELECT * FROM money_accounts";
        return $this->getAllByQuery($query);
    }

    public function getAllTransactions(): array{
        $query = "SELECT * FROM transactions";
        return $this->getAllByQuery($query);
    }

    public function getOrderedTransactions(string $column, string $type, int $limit){
        $query = "SELECT * FROM transactions ORDER BY $column $type LIMIT $limit";
        return $this->getAllByQuery($query);
    }

    public function getAllTransactionTypes(): array {
        $query = "SELECT * FROM transaction_types";
        return $this->getAllByQuery($query);
    }

    public function getAllByQuery(string $query): array {
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function getTotalAmount(string $start, string $end, int $transactionType) {
        $query = "
            SELECT SUM(amount) as total 
            FROM transactions 
            WHERE 
                transaction_type_id = :transaction_type_id
                AND date_time BETWEEN :start AND :end    
            ";

            $stmt = $this->pdo->prepare($query);

            $stmt->execute([
                ":transaction_type_id" => $transactionType,
                ":start" => $start,
                ":end" => $end
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row["total"] ?? 0;
    }

    public function transactionTypeExists(int $id): bool {
        $query = "select 1 from transaction_types where id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ":id" => $id
        ]);

        $result = $stmt->fetchColumn() !== false; 

        return $result;
    }

    public function moneyAccountExists(int $id): bool {
        $result = $this->recordExists($id, "money_accounts");

        return $result;
    }

    public function recordExists(int $id, string $table): bool {
        $query = "select 1 from $table where id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ":id" => $id
        ]);

        $result = $stmt->fetchColumn() !== false; 

        return $result;
    }

    public function insertTransaction(array $transaction): bool {
        $query = "insert into transactions (transaction_type_id, amount, date_time, description, money_account_id) values (:transaction_type_id, :amount, :date_time, :description, :money_account_id)";
        
        $stmt = $this->pdo->prepare($query);
        
        $success = $stmt->execute([
            ":transaction_type_id" => $transaction["transactionTypeId"],
            ":amount" => $transaction["amount"],
            ":date_time" => $transaction["dateTime"],
            ":description" => $transaction["description"],
            ":money_account_id" => $transaction["moneyAccountId"]
        ]);

        return $success;
    }

    public function moneyAvailable(int $moneyAccountId): float {
        $query = "select available from money_accounts where id = :moneyAccountId";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([":moneyAccountId" => $moneyAccountId]);
        $result = $stmt->fetchColumn();

        return (float)$result;
    }

    public function updateMoneyAvailable(float $amount, int $moneyAccountId): bool{
        $query = "UPDATE money_accounts SET available = :amount where id = :id";
        
        $stmt = $this->pdo->prepare($query);
        
        $success = $stmt->execute([
            ":amount" => $amount,
            ":id" => $moneyAccountId
        ]);

        return $success;
    }

    public function updateMoney(float $amount, int $moneyAccount, int $transactionType): bool{
        $updateAvailable = 0;
        $available = $this->moneyAvailable($moneyAccount);

        if($transactionType === 1){
            $updateAvailable = $available + $amount; 
        }
        
        else if($transactionType === 2 ){

            if($amount > $available){
                return false;
            }
            else{
                $updateAvailable = $available - $amount;
            }
        }

        $result = $this->updateMoneyAvailable($updateAvailable, $moneyAccount);
            
        return $result;
    }

    public function getAllTransactionsWithInnerJoin(){

        $query = "SELECT
                    transactions.id,
                    transaction_types.`name` AS transaction_type_name,
                    amount, 
                    date_time,
                    `description`,
                    money_accounts.`name` AS money_account_name
                FROM
                    transactions
                INNER JOIN transaction_types ON transactions.transaction_type_id = transaction_types.id
                INNER JOIN	money_accounts ON transactions.money_account_id = money_accounts.id
                ORDER BY date_time DESC"
        ;

        
        return $this->getAllByQuery($query);
    }

    public function getFilteredTransactions($filter){

        if(!isset($filter["inputSearch"])){
            echo "Ha ocurrido un error con el filtro";
            return;
        }

        $inputSearch = $filter["inputSearch"];

        $query = "SELECT
                    transactions.id,
                    transaction_types.`name` AS transactionTypeName,
                    amount, 
                    date_time,
                    `description`,
                    money_accounts.`name` AS moneyAccountName
                FROM
                    transactions
                INNER JOIN transaction_types ON transactions.transaction_type_id = transaction_types.id
                INNER JOIN	money_accounts ON transactions.money_account_id = money_accounts.id ";

        if($inputSearch !== ""){
            //esto verifica si se ingreso una fecha con formato dd-mm-yyyy
            $inputSearch = strtotime($inputSearch) !== false ? date("y-m-d", strtotime($inputSearch)) : $inputSearch;

            $query .= "WHERE 1!=1 OR transaction_types.`name` = :inputSearch OR amount = :inputSearch OR `description` = :inputSearch OR money_accounts.`name` = :inputSearch OR DATE(date_time) = :inputSearch ";
        }

        $query .= "ORDER BY date_time DESC";

        $stmt = $this->pdo->prepare($query);

        if($inputSearch === ""){
            $stmt->execute();
        }
        else{
            $stmt->execute([
                ":inputSearch" => $inputSearch
            ]);
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
}