<?php

require_once __DIR__ . '/../Logger/Logger.php';

class BaseController {

	protected string $modelClass;
	protected string $repositoryClass;

	public function __construct(string $modelClass, string $repositoryClass) {
		$this->modelClass = $modelClass;
		$this->repositoryClass = $repositoryClass;
	}

	public function handle_method(array $request, string $method) {
		throw new Exception("handle_method() not implemented in " . get_class($this));
	}

	public function getAll() {
		$repository = new $this->repositoryClass();
		Logger::log("Fetching all records from " . $this->repositoryClass);
		return json_encode($repository->getAll());
	}

	public function get($id) {
		$repository = new $this->repositoryClass();
		return json_encode($repository->get($id));
	}

	public function create() {
		$body = json_decode(file_get_contents('php://input'), true);
		$repository = new $this->repositoryClass();
		return json_encode($repository->create($body));
	}

	public function update($id) {
		$body = json_decode(file_get_contents('php://input'), true);
		$repository = new $this->repositoryClass();
		return json_encode($repository->update($id, $body));
	}

	public function delete($id) {
		$repository = new $this->repositoryClass();
		return json_encode($repository->delete($id));
	}


	// Helper functions
	protected function isId($input): bool {
		return is_numeric($input) && $input > 0;
	}
	
}