<?php

require_once __DIR__ . '/Database.php';

abstract class BaseRepository extends Database
{
    protected string $table;
    protected string $modelClass;

    public function getAll(): array
    {
		try {
			$statement = $this->connect()->prepare("SELECT * FROM {$this->table}");
			$statement->execute();
			return [
				'ok'   => true, 
				'data' => $statement->fetchAll(PDO::FETCH_CLASS, $this->modelClass)
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false, 
				'message' => $e->getMessage()
			];
		}
    }

    public function get(int $id): array
    {
		if (!is_numeric($id)) {
			return [
				'ok'      => false, 
				'message' => 'ID must be a number'
			];
		}

		if ($id < 1) {
			return [
				'ok'      => false, 
				'message' => 'ID must be greater than 0'
			];
		}

		try {
			$statement = $this->connect()->prepare("SELECT * FROM {$this->table} WHERE {$this->table}Id = :id");
			$statement->execute(['id' => $id]);
			$statement->setFetchMode(PDO::FETCH_CLASS, $this->modelClass);
			$result = $statement->fetch();

			if ($result) {
				return [
					'ok'   => true, 
					'data' => $result
				];
			} else {
				return [
					'ok'      => false, 
					'message' => 'No record found'
				];
			}
			
		} catch (PDOException $e) {
			return [
				'ok'      => false,
				'message' => $e->getMessage()
			];
		}
    }

	public function create(array $data): array {
		try {
			$columns = implode(", ", array_keys($data));
			$placeholders = ":" . implode(", :", array_keys($data));

			$sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
			$statement = $this->connect()->prepare($sql);
			$statement->execute($data);

			return [
				'ok'      => true, 
				'message' => 'Record created successfully'
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false, 
				'message' => $e->getMessage()
			];
		}
	}

	public function update(int $id, array $data): array {
		try {
			$set = "";
			foreach ($data as $key => $value) {
				$set .= "$key = :$key, ";
			}
			$set = rtrim($set, ", ");

			$sql = "UPDATE {$this->table} SET $set WHERE {$this->table}Id = :id";
			$data['id'] = $id;
			$statement = $this->connect()->prepare($sql);
			$statement->execute($data);

			return [
				'ok'      => true, 
				'message' => 'Record updated successfully'
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false, 
				'message' => $e->getMessage()
			];
		}
	}

	public function delete(int $id): array {
		try {
			$sql = "DELETE FROM {$this->table} WHERE {$this->table}Id = :id";
			$statement = $this->connect()->prepare($sql);
			$statement->execute(['id' => $id]);

			if ($statement->rowCount() === 0) {
				return [
					'ok'      => false, 
					'message' => 'No record found to delete'
				];
			}
			
			return [
				'ok'      => true, 
				'message' => 'Record deleted successfully'
			];
		} catch (PDOException $e) {
			return [
				'ok'      => false, 
				'message' => $e->getMessage()
			];
		}
	}
}

